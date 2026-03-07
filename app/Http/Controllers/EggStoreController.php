<?php

namespace App\Http\Controllers;

use App\Models\BirdBatch;
use App\Models\EggSale;
use App\Models\MarketOrder;
use App\Models\MarketOrderItem;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EggStoreController extends Controller
{
    public const CART_KEY = 'egg_store_cart';

    public function index()
    {
        $priceCrate = PaymentSetting::getEggMarketPricePerCrate();
        $pricePiece = PaymentSetting::getEggMarketPricePerPiece();
        $eggsPerCrate = PaymentSetting::getEggMarketEggsPerCrate();
        $gateway = PaymentSetting::getActiveGateway();
        $cart = session(self::CART_KEY, ['items' => []]);
        $cartCount = collect($cart['items'] ?? [])->sum('quantity');

        return view('store.index', compact('priceCrate', 'pricePiece', 'eggsPerCrate', 'gateway', 'cartCount'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'unit_type' => 'required|in:crate,piece',
            'quantity' => 'required|integer|min:1|max:100',
        ]);
        $priceCrate = PaymentSetting::getEggMarketPricePerCrate();
        $pricePiece = PaymentSetting::getEggMarketPricePerPiece();
        $unitPrice = $request->unit_type === 'crate' ? $priceCrate : $pricePiece;
        if ($unitPrice <= 0) {
            return back()->with('error', 'This option is not available. Please set prices in Payment Settings.');
        }
        $cart = session(self::CART_KEY, ['items' => []]);
        $items = $cart['items'] ?? [];
        $found = false;
        foreach ($items as $i => $item) {
            if ($item['unit_type'] === $request->unit_type) {
                $items[$i]['quantity'] += (int) $request->quantity;
                $items[$i]['total'] = $items[$i]['quantity'] * $items[$i]['unit_price'];
                $found = true;
                break;
            }
        }
        if (! $found) {
            $qty = (int) $request->quantity;
            $items[] = [
                'unit_type' => $request->unit_type,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'total' => $qty * $unitPrice,
            ];
        }
        session([self::CART_KEY => ['items' => $items]]);

        return redirect()->route('store.cart')->with('success', 'Added to cart.');
    }

    public function cart()
    {
        $cart = session(self::CART_KEY, ['items' => []]);
        $items = $cart['items'] ?? [];
        $subtotal = collect($items)->sum('total');
        $gateway = PaymentSetting::getActiveGateway();

        return view('store.cart', compact('items', 'subtotal', 'gateway'));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'items' => 'array',
            'items.*.unit_type' => 'required|in:crate,piece',
            'items.*.quantity' => 'required|integer|min:0',
        ]);
        $priceCrate = PaymentSetting::getEggMarketPricePerCrate();
        $pricePiece = PaymentSetting::getEggMarketPricePerPiece();
        $newItems = [];
        foreach ($request->input('items', []) as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty <= 0) {
                continue;
            }
            $unitPrice = $item['unit_type'] === 'crate' ? $priceCrate : $pricePiece;
            $newItems[] = [
                'unit_type' => $item['unit_type'],
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'total' => $qty * $unitPrice,
            ];
        }
        session([self::CART_KEY => ['items' => $newItems]]);

        return redirect()->route('store.cart')->with('success', 'Cart updated.');
    }

    public function removeFromCart(string $unitType)
    {
        $cart = session(self::CART_KEY, ['items' => []]);
        $items = array_values(array_filter($cart['items'] ?? [], fn ($i) => $i['unit_type'] !== $unitType));
        session([self::CART_KEY => ['items' => $items]]);

        return redirect()->route('store.cart')->with('success', 'Item removed.');
    }

    public function checkout()
    {
        $cart = session(self::CART_KEY, ['items' => []]);
        $items = $cart['items'] ?? [];
        if (empty($items)) {
            return redirect()->route('store.index')->with('info', 'Your cart is empty.');
        }
        $subtotal = collect($items)->sum('total');
        $gateway = PaymentSetting::getActiveGateway();
        if (! $gateway) {
            return redirect()->route('store.index')->with('error', 'Online payment is not configured. Please contact us to place an order.');
        }

        return view('store.checkout', compact('items', 'subtotal', 'gateway'));
    }

    public function processCheckout(Request $request)
    {
        $cart = session(self::CART_KEY, ['items' => []]);
        $items = $cart['items'] ?? [];
        if (empty($items)) {
            return redirect()->route('store.index')->with('error', 'Your cart is empty.');
        }
        $gateway = PaymentSetting::getActiveGateway();
        if (! $gateway) {
            return redirect()->route('store.index')->with('error', 'Payment is not configured.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:50',
            'wants_delivery' => 'nullable|boolean',
            'delivery_address' => 'nullable|string|max:500',
            'delivery_notes' => 'nullable|string|max:500',
        ]);

        $subtotal = collect($items)->sum('total');
        $wantsDelivery = (bool) $request->input('wants_delivery');

        $order = MarketOrder::create([
            'order_number' => MarketOrder::generateOrderNumber(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'delivery_address' => $wantsDelivery ? $request->delivery_address : null,
            'delivery_notes' => $wantsDelivery ? $request->delivery_notes : null,
            'wants_delivery' => $wantsDelivery,
            'subtotal' => $subtotal,
            'total_amount' => $subtotal,
            'payment_gateway' => $gateway,
            'status' => MarketOrder::STATUS_PENDING,
        ]);

        foreach ($items as $item) {
            MarketOrderItem::create([
                'market_order_id' => $order->id,
                'unit_type' => $item['unit_type'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ]);
        }

        session()->forget(self::CART_KEY);

        if ($gateway === 'paystack') {
            $url = $this->initializePaystack($order);
            if ($url) {
                return redirect($url);
            }
            return redirect()->route('store.checkout')->with('error', 'Could not start payment. Please try again.');
        }

        if ($gateway === 'hubtel') {
            $url = $this->initializeHubtel($order);
            if ($url) {
                return redirect($url);
            }
            return redirect()->route('store.order.pending', $order)->with('info', 'Order placed. You will receive a payment link shortly.');
        }

        return redirect()->route('store.order.pending', $order);
    }

    public function paymentCallback(Request $request)
    {
        $reference = $request->query('reference');
        if (! $reference) {
            return redirect()->route('store.index')->with('error', 'Invalid payment reference.');
        }
        $verified = $this->verifyPaystack($reference);
        if (! $verified || ($verified['data']['status'] ?? '') !== 'success') {
            return redirect()->route('store.index')->with('error', 'Payment could not be verified.');
        }
        $order = MarketOrder::where('payment_reference', $reference)->first();
        if (! $order || $order->status !== MarketOrder::STATUS_PENDING) {
            return redirect()->route('store.index')->with('error', 'Order not found or already processed.');
        }
        $order->update(['status' => MarketOrder::STATUS_PAID]);
        // Egg sales are created only when staff marks the order as "Complete" in Egg Sales > Online Store Sales.

        return redirect()->route('store.order.success', $order)->with('success', 'Payment successful. Thank you for your order!');
    }

    public function orderPending(MarketOrder $order)
    {
        return view('store.order-pending', compact('order'));
    }

    public function orderSuccess(MarketOrder $order)
    {
        return view('store.order-success', compact('order'));
    }

    protected function initializePaystack(MarketOrder $order): ?string
    {
        $secret = config('services.paystack.secret_key');
        $baseUrl = rtrim(config('services.paystack.base_url', 'https://api.paystack.co'), '/');
        if (! $secret) {
            return null;
        }
        $reference = 'EGG_' . $order->id . '_' . time();
        $order->update(['payment_reference' => $reference]);

        $data = [
            'email' => $order->customer_email,
            'amount' => (int) round($order->total_amount * 100),
            'currency' => 'GHS',
            'reference' => $reference,
            'callback_url' => route('store.payment.callback'),
            'metadata' => ['order_number' => $order->order_number, 'order_id' => $order->id],
        ];

        $ch = curl_init($baseUrl . '/transaction/initialize');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $secret,
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            Log::error('Paystack init error: ' . $err);
            return null;
        }
        $result = json_decode($response, true);
        if (empty($result['status']) || empty($result['data']['authorization_url'])) {
            Log::error('Paystack init response: ' . $response);
            return null;
        }
        return $result['data']['authorization_url'];
    }

    protected function verifyPaystack(string $reference): ?array
    {
        $secret = config('services.paystack.secret_key');
        $baseUrl = rtrim(config('services.paystack.base_url', 'https://api.paystack.co'), '/');
        if (! $secret) {
            return null;
        }
        $ch = curl_init($baseUrl . '/transaction/verify/' . rawurlencode($reference));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $secret],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    /** Hubtel: create checkout and return redirect URL if available. */
    protected function initializeHubtel(MarketOrder $order): ?string
    {
        $clientId = config('services.hubtel.client_id');
        $clientSecret = config('services.hubtel.client_secret');
        if (! $clientId || ! $clientSecret) {
            return null;
        }
        $reference = 'EGG_' . $order->id . '_' . time();
        $order->update(['payment_reference' => $reference]);

        $payload = [
            'totalAmount' => (float) $order->total_amount,
            'description' => 'Egg order ' . $order->order_number,
            'callbackUrl' => route('store.payment.callback') . '?reference=' . urlencode($reference),
            'returnUrl' => route('store.order.success', $order),
            'cancellationUrl' => route('store.cart'),
            'clientReference' => $reference,
        ];

        $auth = base64_encode($clientId . ':' . $clientSecret);
        $ch = curl_init('https://api-txn.hubtel.com/pos/initiate');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            Log::error('Hubtel init error: ' . $err);
            return null;
        }
        $result = json_decode($response, true);
        $url = $result['data']['checkoutUrl'] ?? $result['checkoutUrl'] ?? null;
        return $url;
    }

    protected function recordEggSalesForOrder(MarketOrder $order): void
    {
        $batchId = PaymentSetting::getEggMarketBatchId();
        if (! $batchId) {
            return;
        }
        $batch = BirdBatch::find($batchId);
        if (! $batch) {
            return;
        }
        foreach ($order->items as $item) {
            EggSale::create([
                'bird_batch_id' => $batchId,
                'date' => $order->created_at->toDateString(),
                'quantity_sold' => $item->quantity,
                'unit_type' => $item->unit_type,
                'price_per_unit' => $item->unit_price,
                'buyer_name' => $order->customer_name,
                'buyer_contact' => $order->customer_phone,
                'notes' => 'Online order ' . $order->order_number,
            ]);
        }
    }
}
