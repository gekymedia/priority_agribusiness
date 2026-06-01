<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BirdBatch;
use App\Models\BirdBatchRecord;
use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\Employee;
use App\Models\PaymentSetting;
use App\Services\PriorityBankIntegrationService;
use App\Support\PhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UssdApiController extends Controller
{
    /**
     * Check if a phone number belongs to an approved agribusiness staff member.
     */
    public function staffCheck(Request $request): JsonResponse
    {
        $phone = (string) $request->query('phone', '');
        $employee = $this->findStaffByPhone($phone);

        if (! $employee) {
            return response()->json([
                'success' => true,
                'data' => ['is_staff' => false],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_staff' => true,
                'employee_id' => $employee->employee_id,
                'name' => $employee->full_name,
                'access_level' => $employee->access_level,
                'farm_id' => $employee->farm_id,
            ],
        ]);
    }

    /**
     * List active batches for USSD menus.
     */
    public function batches(Request $request): JsonResponse
    {
        $phone = (string) $request->query('phone', '');
        $employee = $this->findStaffByPhone($phone);
        if (! $employee) {
            return response()->json(['success' => false, 'message' => 'Unauthorized staff phone.'], 403);
        }

        $purpose = $request->query('purpose', 'all');
        $query = BirdBatch::query()
            ->where('status', 'active')
            ->with(['farm', 'house'])
            ->orderBy('batch_code');

        if ($purpose === 'egg') {
            $query->whereIn('purpose', ['egg_production', 'layer']);
        }

        $batches = $query->get()->map(function (BirdBatch $batch) {
            return [
                'id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'farm_name' => $batch->farm?->name,
                'house_name' => $batch->house?->name,
                'purpose' => $batch->purpose,
            ];
        })->values();

        $defaultBatchId = PaymentSetting::getEggMarketBatchId();
        $defaults = [
            'egg_batch_id' => $defaultBatchId,
            'price_per_crate' => PaymentSetting::getEggMarketPricePerCrate(),
            'price_per_piece' => PaymentSetting::getEggMarketPricePerPiece(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'batches' => $batches,
                'defaults' => $defaults,
            ],
        ]);
    }

    /**
     * Record an egg sale from USSD.
     */
    public function recordEggSale(Request $request): JsonResponse
    {
        $phone = (string) $request->input('phone', '');
        $employee = $this->findStaffByPhone($phone);
        if (! $employee) {
            return response()->json(['success' => false, 'message' => 'Unauthorized staff phone.'], 403);
        }

        $data = $request->validate([
            'bird_batch_id' => 'nullable|exists:bird_batches,id',
            'quantity_sold' => 'required|integer|min:1',
            'unit_type' => 'nullable|string|in:tray,piece,crate',
            'price_per_unit' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
            'buyer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $unitType = $data['unit_type'] ?? 'crate';
        $batchId = $data['bird_batch_id'] ?? PaymentSetting::getEggMarketBatchId();

        if (! $batchId || ! BirdBatch::find($batchId)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid bird batch configured for egg sales.',
            ], 422);
        }

        $pricePerUnit = $data['price_per_unit'] ?? match ($unitType) {
            'piece' => PaymentSetting::getEggMarketPricePerPiece(),
            'tray' => PaymentSetting::getEggMarketPricePerCrate() > 0
                ? round(PaymentSetting::getEggMarketPricePerCrate() / max(1, PaymentSetting::getEggMarketEggsPerCrate() / 30), 2)
                : 0,
            default => PaymentSetting::getEggMarketPricePerCrate(),
        };

        $notes = trim(($data['notes'] ?? '') . ' Recorded via USSD by ' . $employee->full_name . ' (' . $phone . ').');

        $eggSale = EggSale::create([
            'bird_batch_id' => $batchId,
            'date' => $data['date'] ?? now()->toDateString(),
            'quantity_sold' => $data['quantity_sold'],
            'unit_type' => $unitType,
            'price_per_unit' => $pricePerUnit,
            'buyer_name' => $data['buyer_name'] ?? null,
            'buyer_contact' => $phone,
            'notes' => $notes,
        ]);

        try {
            (new PriorityBankIntegrationService())->pushEggSale($eggSale);
        } catch (\Throwable $e) {
            Log::error('USSD egg sale: Priority Bank push failed', [
                'egg_sale_id' => $eggSale->id,
                'error' => $e->getMessage(),
            ]);
        }

        $total = round($eggSale->quantity_sold * $eggSale->price_per_unit, 2);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $eggSale->id,
                'quantity_sold' => $eggSale->quantity_sold,
                'unit_type' => $eggSale->unit_type,
                'price_per_unit' => (float) $eggSale->price_per_unit,
                'total_amount' => $total,
                'date' => $eggSale->date->toDateString(),
            ],
            'message' => 'Egg sale recorded successfully.',
        ], 201);
    }

    /**
     * Record daily mortality from USSD.
     */
    public function recordMortality(Request $request): JsonResponse
    {
        $phone = (string) $request->input('phone', '');
        $employee = $this->findStaffByPhone($phone);
        if (! $employee) {
            return response()->json(['success' => false, 'message' => 'Unauthorized staff phone.'], 403);
        }

        $data = $request->validate([
            'bird_batch_id' => 'nullable|exists:bird_batches,id',
            'mortality_count' => 'required|integer|min:0',
            'cull_count' => 'nullable|integer|min:0',
            'record_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $batchId = $data['bird_batch_id'] ?? PaymentSetting::getEggMarketBatchId();
        if (! $batchId || ! BirdBatch::where('id', $batchId)->where('status', 'active')->exists()) {
            $fallback = BirdBatch::where('status', 'active')->orderBy('batch_code')->value('id');
            $batchId = $fallback;
        }

        if (! $batchId) {
            return response()->json([
                'success' => false,
                'message' => 'No active bird batch found.',
            ], 422);
        }

        $recordDate = $data['record_date'] ?? now()->toDateString();
        $notes = trim(($data['notes'] ?? '') . ' Recorded via USSD by ' . $employee->full_name . ' (' . $phone . ').');

        $record = BirdBatchRecord::query()
            ->where('bird_batch_id', $batchId)
            ->whereDate('record_date', $recordDate)
            ->first();

        if ($record) {
            $record->update([
                'mortality_count' => $data['mortality_count'],
                'cull_count' => $data['cull_count'] ?? $record->cull_count ?? 0,
                'notes' => $notes,
            ]);
            $created = false;
        } else {
            $record = BirdBatchRecord::create([
                'bird_batch_id' => $batchId,
                'record_date' => $recordDate,
                'mortality_count' => $data['mortality_count'],
                'cull_count' => $data['cull_count'] ?? 0,
                'feed_used_kg' => 0,
                'notes' => $notes,
            ]);
            $created = true;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $record->id,
                'bird_batch_id' => $record->bird_batch_id,
                'mortality_count' => $record->mortality_count,
                'cull_count' => $record->cull_count,
                'record_date' => $record->record_date instanceof \DateTimeInterface
                    ? $record->record_date->format('Y-m-d')
                    : (string) $record->record_date,
                'updated' => ! $created,
            ],
            'message' => $created ? 'Mortality recorded successfully.' : 'Today\'s mortality record updated.',
        ], $created ? 201 : 200);
    }

    /**
     * Record daily egg production from USSD.
     */
    public function recordEggProduction(Request $request): JsonResponse
    {
        $phone = (string) $request->input('phone', '');
        $employee = $this->findStaffByPhone($phone);
        if (! $employee) {
            return response()->json(['success' => false, 'message' => 'Unauthorized staff phone.'], 403);
        }

        $data = $request->validate([
            'bird_batch_id' => 'nullable|exists:bird_batches,id',
            'eggs_collected' => 'required|integer|min:0',
            'cracked_or_damaged' => 'nullable|integer|min:0',
            'eggs_used_internal' => 'nullable|integer|min:0',
            'date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $cracked = (int) ($data['cracked_or_damaged'] ?? 0);
        $internal = (int) ($data['eggs_used_internal'] ?? 0);
        if ($cracked + $internal > $data['eggs_collected']) {
            return response()->json([
                'success' => false,
                'message' => 'Cracked and internal eggs cannot exceed total collected.',
            ], 422);
        }

        $batchId = $data['bird_batch_id'] ?? PaymentSetting::getEggMarketBatchId();
        if (! $batchId || ! BirdBatch::where('id', $batchId)->whereIn('purpose', ['egg_production', 'layer'])->where('status', 'active')->exists()) {
            $fallback = BirdBatch::query()
                ->where('status', 'active')
                ->whereIn('purpose', ['egg_production', 'layer'])
                ->orderBy('batch_code')
                ->value('id');
            $batchId = $fallback;
        }

        if (! $batchId) {
            return response()->json([
                'success' => false,
                'message' => 'No active layer batch found.',
            ], 422);
        }

        $recordDate = $data['date'] ?? now()->toDateString();
        $notes = trim(($data['notes'] ?? '') . ' Recorded via USSD by ' . $employee->full_name . ' (' . $phone . ').');

        $record = EggProduction::query()
            ->where('bird_batch_id', $batchId)
            ->whereDate('date', $recordDate)
            ->first();

        $payload = [
            'bird_batch_id' => $batchId,
            'date' => $recordDate,
            'eggs_collected' => $data['eggs_collected'],
            'cracked_or_damaged' => $cracked,
            'eggs_used_internal' => $internal,
            'egg_size_breakdown' => false,
            'eggs_large' => 0,
            'eggs_medium' => 0,
            'eggs_small' => 0,
            'notes' => $notes,
        ];

        if ($record) {
            $record->update($payload);
            $created = false;
        } else {
            $record = EggProduction::create($payload);
            $created = true;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $record->id,
                'bird_batch_id' => $record->bird_batch_id,
                'eggs_collected' => $record->eggs_collected,
                'cracked_or_damaged' => $record->cracked_or_damaged,
                'eggs_used_internal' => $record->eggs_used_internal,
                'good_eggs' => $record->remainingEggs(),
                'date' => $record->date instanceof \DateTimeInterface
                    ? $record->date->format('Y-m-d')
                    : (string) $record->date,
                'updated' => ! $created,
            ],
            'message' => $created ? 'Egg production recorded successfully.' : 'Today\'s egg production updated.',
        ], $created ? 201 : 200);
    }

    protected function findStaffByPhone(string $phone): ?Employee
    {
        $variants = PhoneNormalizer::variants($phone);
        if ($variants === []) {
            return null;
        }

        return Employee::query()
            ->where('is_active', true)
            ->where('status', 'approved')
            ->whereNotIn('access_level', ['viewer'])
            ->where(function ($query) use ($variants) {
                foreach ($variants as $variant) {
                    $query->orWhere('phone', $variant);
                }
            })
            ->first();
    }
}
