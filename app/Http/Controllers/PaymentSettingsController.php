<?php

namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    public const SECRET_MASK = '********';

    public function index()
    {
        $settings = $this->getSettingsWithFallback();
        $channels = $this->getChannelsStatus($settings);
        $activeGateway = PaymentSetting::getActiveGateway();

        return view('settings.payment', compact('channels', 'settings', 'activeGateway'));
    }

    protected function getSettingsWithFallback(): array
    {
        $fromDb = function (string $key, $configFallback) {
            $v = PaymentSetting::get($key);
            return $v !== null && $v !== '' ? $v : $configFallback;
        };
        return [
            'hubtel_client_id' => $fromDb('hubtel_client_id', config('services.hubtel.client_id', '')),
            'hubtel_client_secret' => $this->secretDisplayValue('hubtel_client_secret', config('services.hubtel.client_secret')),
            'hubtel_api_key' => $fromDb('hubtel_api_key', config('services.hubtel.api_key', '')),
            'hubtel_api_secret' => $this->secretDisplayValue('hubtel_api_secret', config('services.hubtel.api_secret')),
            'hubtel_merchant_account_number' => $fromDb('hubtel_merchant_account_number', config('services.hubtel.merchant_account_number', '')),
            'paystack_public_key' => $fromDb('paystack_public_key', config('services.paystack.public_key', '')),
            'paystack_secret_key' => $this->secretDisplayValue('paystack_secret_key', config('services.paystack.secret_key')),
            'paystack_base_url' => $fromDb('paystack_base_url', config('services.paystack.base_url', 'https://api.paystack.co')),
            'paystack_webhook_secret' => $this->secretDisplayValue('paystack_webhook_secret', config('services.paystack.webhook_secret')),
            'egg_market_price_per_crate' => $fromDb('egg_market_price_per_crate', ''),
            'egg_market_price_per_piece' => $fromDb('egg_market_price_per_piece', ''),
            'egg_market_eggs_per_crate' => $fromDb('egg_market_eggs_per_crate', '30'),
            'egg_market_batch_id' => $fromDb('egg_market_batch_id', ''),
        ];
    }

    protected function secretDisplayValue(string $key, $configFallback): string
    {
        $v = PaymentSetting::get($key);
        if ($v !== null && $v !== '') {
            return self::SECRET_MASK;
        }
        if ($configFallback !== null && $configFallback !== '') {
            return self::SECRET_MASK;
        }
        return '';
    }

    protected function setSecretIfChanged(string $key, Request $request): void
    {
        $value = $request->input($key);
        if ($value === null) {
            return;
        }
        $value = is_string($value) ? trim($value) : '';
        if ($value === '' || $value === self::SECRET_MASK || preg_match('/^\*+$/', $value)) {
            return;
        }
        PaymentSetting::set($key, $value);
    }

    protected function getChannelsStatus(array $settings): array
    {
        $hubtelConfigured = PaymentSetting::isHubtelConfigured();
        $paystackConfigured = PaymentSetting::isPaystackConfigured();
        $active = PaymentSetting::getActiveGateway();

        return [
            'hubtel' => [
                'name' => 'Hubtel',
                'configured' => $hubtelConfigured,
                'description' => 'Collect payments via Hubtel. When both are configured, Hubtel takes precedence.',
                'is_active' => $active === 'hubtel',
            ],
            'paystack' => [
                'name' => 'Paystack',
                'configured' => $paystackConfigured,
                'description' => 'Collect payments via Paystack (cards, mobile money). Used when Hubtel is not configured.',
                'is_active' => $active === 'paystack',
            ],
        ];
    }

    public function update(Request $request)
    {
        $request->validate([
            'hubtel_client_id' => 'nullable|string|max:255',
            'hubtel_client_secret' => 'nullable|string|max:255',
            'hubtel_api_key' => 'nullable|string|max:255',
            'hubtel_api_secret' => 'nullable|string|max:255',
            'hubtel_merchant_account_number' => 'nullable|string|max:100',
            'paystack_public_key' => 'nullable|string|max:255',
            'paystack_secret_key' => 'nullable|string|max:255',
            'paystack_base_url' => 'nullable|string|url|max:255',
            'paystack_webhook_secret' => 'nullable|string|max:255',
            'egg_market_price_per_crate' => 'nullable|numeric|min:0',
            'egg_market_price_per_piece' => 'nullable|numeric|min:0',
            'egg_market_eggs_per_crate' => 'nullable|integer|min:1',
            'egg_market_batch_id' => 'nullable|exists:bird_batches,id',
        ]);

        $textKeys = [
            'hubtel_client_id', 'hubtel_api_key', 'hubtel_merchant_account_number',
            'paystack_public_key', 'paystack_base_url',
            'egg_market_price_per_crate', 'egg_market_price_per_piece', 'egg_market_eggs_per_crate', 'egg_market_batch_id',
        ];
        foreach ($textKeys as $key) {
            PaymentSetting::set($key, $request->input($key));
        }
        $this->setSecretIfChanged('hubtel_client_secret', $request);
        $this->setSecretIfChanged('hubtel_api_secret', $request);
        $this->setSecretIfChanged('paystack_secret_key', $request);
        $this->setSecretIfChanged('paystack_webhook_secret', $request);

        return redirect()->route('payment-settings.index')
            ->with('success', 'Payment settings saved.');
    }
}
