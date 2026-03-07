<?php

namespace App\Services;

use App\Models\BirdBatch;
use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\Farm;
use App\Models\House;
use App\Models\Field;
use App\Models\Planting;
use App\Models\Task;
use App\Models\ExpenseCategory;
use App\Models\PoultryExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FarmAnalyticsService
{
    public function getFarmDataSummary(): string
    {
        $farms = Farm::with(['houses', 'fields'])->get();
        $batches = BirdBatch::with('farm')->get();
        $layerBatches = $batches->whereIn('purpose', ['egg_production', 'layer']);
        $last6Months = Carbon::now()->subMonths(6);

        $production = EggProduction::where('date', '>=', $last6Months)
            ->selectRaw("SUM(eggs_collected) as total, SUM(cracked_or_damaged) as damaged, SUM(eggs_used_internal) as internal")
            ->first();
        $eggSales = EggSale::where('date', '>=', $last6Months)->get();
        $salesByUnit = $eggSales->groupBy('unit_type');
        $salesPieces = $eggSales->where('unit_type', 'piece')->sum('quantity_sold');
        $salesCrates = $eggSales->where('unit_type', 'crate')->sum('quantity_sold');
        $eggsPerCrate = 30;
        if (class_exists(\App\Models\PaymentSetting::class) && \Illuminate\Support\Facades\Schema::hasTable('payment_settings')) {
            $eggsPerCrate = max(1, (int) \App\Models\PaymentSetting::getEggMarketEggsPerCrate());
        }
        $totalEggsSold = $salesPieces + $salesCrates * $eggsPerCrate;
        $revenueEggs = $eggSales->sum(fn ($s) => $s->quantity_sold * ($s->price_per_unit ?? 0));

        $expenses = PoultryExpense::where('date', '>=', $last6Months);
        $totalExpenses = $expenses->sum('amount');
        $expenseByCategory = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('expense_categories')) {
            $totalsByCategoryId = PoultryExpense::where('date', '>=', $last6Months)
                ->selectRaw('category_id, SUM(amount) as total')
                ->groupBy('category_id')
                ->pluck('total', 'category_id');
            $categoryIds = $totalsByCategoryId->keys()->filter()->values()->all();
            $categories = $categoryIds ? ExpenseCategory::whereIn('id', $categoryIds)->pluck('name', 'id') : collect();
            foreach ($totalsByCategoryId as $categoryId => $total) {
                $name = $categoryId ? ($categories[$categoryId] ?? 'Uncategorized') : 'Uncategorized';
                $expenseByCategory[$name] = $total;
            }
        }

        $pendingTasks = Task::where('status', 'pending')->count();
        $overdueTasks = Task::where('status', 'pending')->where('due_date', '<', now()->toDateString())->count();
        $plantings = Planting::all();
        $growingPlantings = $plantings->where('status', 'growing')->count();

        $summary = [
            'Farms' => $farms->count() . ' total. ' . $farms->map(fn ($f) => $f->name . ' (' . $f->farm_type . '): ' . $f->houses->count() . ' houses, ' . $f->fields->count() . ' fields')->implode('. '),
            'Bird batches' => $batches->count() . ' total, ' . $layerBatches->count() . ' layer. Active: ' . $batches->where('status', 'active')->count() . '. Total birds (arrived): ' . $batches->sum('quantity_arrived'),
            'Egg production (last 6 months)' => 'Collected: ' . ($production->total ?? 0) . ', Damaged: ' . ($production->damaged ?? 0) . ', Internal use: ' . ($production->internal ?? 0) . '. Net available: ' . max(0, ($production->total ?? 0) - ($production->damaged ?? 0) - ($production->internal ?? 0)),
            'Egg sales (last 6 months)' => 'Total eggs sold (equiv): ' . $totalEggsSold . '. Revenue (eggs): ' . number_format($revenueEggs, 2),
            'Expenses (last 6 months)' => 'Total: ' . number_format($totalExpenses, 2) . '. By category: ' . json_encode($expenseByCategory),
            'Tasks' => 'Pending: ' . $pendingTasks . ', Overdue: ' . $overdueTasks,
            'Plantings' => $plantings->count() . ' total, ' . $growingPlantings . ' currently growing.',
        ];

        return "Farm data summary (as of " . now()->toDateString() . "):\n\n" . collect($summary)->map(fn ($v, $k) => "**{$k}**: {$v}")->implode("\n\n");
    }

    public function getRecommendations(): string
    {
        $data = $this->getFarmDataSummary();

        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            return 'AI analytics is not configured. Please set OPENAI_API_KEY in your .env file.';
        }

        $systemPrompt = <<<'PROMPT'
You are an expert agribusiness analyst for a poultry and crop farm. Given a summary of the farm's data, provide clear, actionable recommendations. Consider:

1. **Egg production vs sales**: Is production keeping up with demand? Surplus or shortfall?
2. **Layer batch health**: Stock levels, age structure, replacement planning.
3. **Expenses vs revenue**: Profitability, cost control, category-wise advice.
4. **Tasks**: Overdue or too many pending tasks – suggest prioritization or process improvements.
5. **Crops/plantings**: Diversification, timing, resource use.
6. **Operations**: Housing capacity, labour, feed, medication schedules if mentioned.
7. **Risks**: Identify any obvious risks (e.g. single batch dependency, cash flow).
8. **Quick wins**: 2–3 things they can do immediately to improve.

Write in clear, concise bullet points or short paragraphs. Be specific and practical. If data is missing or very limited, say so and suggest what to track. Use a friendly, professional tone.
PROMPT;

        try {
            $response = Http::withToken($apiKey)
                ->timeout(config('services.openai.timeout', 60))
                ->post(rtrim(config('services.openai.base_url'), '/') . '/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $data],
                    ],
                    'max_tokens' => 1500,
                ]);

            if (!$response->successful()) {
                Log::warning('OpenAI farm analytics failed', ['status' => $response->status(), 'body' => $response->body()]);
                return 'Unable to fetch AI recommendations. Please check your API key and try again. (Status: ' . $response->status() . ')';
            }

            $body = $response->json();
            $content = $body['choices'][0]['message']['content'] ?? '';
            return trim($content) ?: 'No recommendations generated.';
        } catch (\Throwable $e) {
            Log::error('Farm analytics OpenAI error: ' . $e->getMessage());
            return 'An error occurred while generating recommendations: ' . $e->getMessage();
        }
    }
}
