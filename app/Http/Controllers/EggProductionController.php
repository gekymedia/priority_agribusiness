<?php

namespace App\Http\Controllers;

use App\Models\EggProduction;
use App\Models\BirdBatch;
use App\Services\CrudNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EggProductionController extends Controller
{
    /**
     * Parse bulk import text: supports bracket format [date, eggs_collected, cracked_or_damaged, notes]
     * and caretaker-style lines (e.g. "19th January 0 eggs"). Returns array of rows for DB insert.
     */
    protected function parseBulkImportText(string $text, int $defaultYear): array
    {
        $bracketRows = $this->parseBulkImportBracketFormat($text, $defaultYear);
        if (! empty($bracketRows)) {
            return $bracketRows;
        }
        return $this->parseBulkImportCaretakerFormat($text, $defaultYear);
    }

    /**
     * Bracket format: each [date, eggs_collected, cracked_or_damaged, notes] = one row.
     */
    protected function parseBulkImportBracketFormat(string $text, int $defaultYear): array
    {
        if (! preg_match_all('/\[([^\]]*)\]/', $text, $matches)) {
            return [];
        }
        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4, 'may' => 5, 'june' => 6,
            'july' => 7, 'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
        ];
        $rows = [];
        foreach ($matches[1] as $inner) {
            $parts = array_map('trim', explode(',', $inner));
            if (count($parts) < 3) {
                continue;
            }
            $dateStr = $parts[0];
            $eggsCollectedRaw = $parts[1];
            $crackedRaw = $parts[2];
            $notes = count($parts) > 3 ? trim(implode(',', array_slice($parts, 3))) : null;
            if ($notes === '') {
                $notes = null;
            }

            if (! ctype_digit(trim($eggsCollectedRaw)) && ! is_numeric(trim($eggsCollectedRaw))) {
                continue;
            }
            $eggsCollected = (int) $eggsCollectedRaw;
            $crackedOrDamaged = is_numeric(trim($crackedRaw)) ? (int) $crackedRaw : 0;

            $date = $this->parseEggDateString($dateStr, $defaultYear, $months);
            if ($date === null) {
                continue;
            }
            $rows[] = [
                'date' => $date,
                'eggs_collected' => $eggsCollected,
                'cracked_or_damaged' => $crackedOrDamaged,
                'eggs_used_internal' => 0,
                'notes' => $notes,
            ];
        }
        return $rows;
    }

    /**
     * Parse date string like "19th January" or "19th January 2024" to Y-m-d.
     */
    protected function parseEggDateString(string $dateStr, int $defaultYear, array $months): ?string
    {
        $monthPattern = implode('|', array_keys($months));
        if (! preg_match('/^(\d{1,2})(?:st|nd|rd|th)?\s+(' . $monthPattern . ')(?:\s+(\d{4}))?/si', trim($dateStr), $m)) {
            return null;
        }
        $day = (int) $m[1];
        $month = $months[strtolower($m[2])];
        $year = ! empty($m[3]) ? (int) $m[3] : $defaultYear;
        try {
            return Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Caretaker-style: e.g. "19th January 0 eggs" into structured rows.
     */
    protected function parseBulkImportCaretakerFormat(string $text, int $defaultYear): array
    {
        $months = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4, 'may' => 5, 'june' => 6,
            'july' => 7, 'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
        ];
        $monthPattern = implode('|', array_keys($months));
        $dayMonthPattern = '/(\d{1,2})(?:st|nd|rd|th)?\s+(' . $monthPattern . ')\s*(?:(\d{4})\s*)?(.*?)(?=\d{1,2}(?:st|nd|rd|th)?\s+(?:' . $monthPattern . ')|\z)/si';

        $rows = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($text));
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $offset = 0;
            while (preg_match($dayMonthPattern, $line, $m, 0, $offset)) {
                $day = (int) $m[1];
                $month = $months[strtolower($m[2])];
                $year = ! empty($m[3]) ? (int) $m[3] : $defaultYear;
                $rest = trim($m[4] ?? '');
                $offset += strlen($m[0]);

                $collected = 0;
                $damaged = 0;

                // "X eggs 1 broken" or "7 eggs 1 broken"
                if (preg_match('/^(\d+)\s*eggs?\s+(\d+)\s*(?:broken|damage|crack)/i', $rest, $nm)) {
                    $total = (int) $nm[1];
                    $damaged = (int) $nm[2];
                    $collected = max(0, $total - $damaged);
                } elseif (preg_match('/(\d+)\s*(?:damage|damaged|crack)\s*(?:egg|eggs)/i', $rest) || preg_match('/(?:crack|damage)\s*(\d+)\s*egg/i', $rest)) {
                    $damaged = (int) preg_replace('/.*?(\d+).*/', '$1', $rest);
                    $collected = 0;
                } elseif (preg_match('/^(\d+)\s*eggs?/i', $rest, $nm)) {
                    $collected = (int) $nm[1];
                    $damaged = 0;
                }

                try {
                    $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
                $rows[] = [
                    'date' => $date,
                    'eggs_collected' => $collected,
                    'cracked_or_damaged' => $damaged,
                    'eggs_used_internal' => 0,
                    'notes' => $rest !== '' ? $rest : null,
                ];
            }
        }
        return $rows;
    }

    public function bulkImport()
    {
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->orderBy('arrival_date', 'desc')
            ->get();
        return view('egg-productions.bulk-import', compact('batches'));
    }

    public function processBulkImport(Request $request)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'pasted_data' => 'required|string|max:10000',
            'year' => 'nullable|integer|min:2020|max:2030',
        ]);
        $year = (int) ($data['year'] ?? Carbon::now()->year);
        $parsed = $this->parseBulkImportText($data['pasted_data'], $year);
        if (empty($parsed)) {
            return redirect()->route('egg-productions.bulk-import')
                ->withInput()
                ->with('error', 'No valid rows found. Use bracket format [date, eggs_collected, cracked_or_damaged, notes] or lines like "19th January 0 eggs".');
        }
        $batchId = (int) $data['bird_batch_id'];
        $existingDates = EggProduction::where('bird_batch_id', $batchId)
            ->whereIn('date', array_column($parsed, 'date'))
            ->pluck('date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->all();
        $toInsert = [];
        foreach ($parsed as $row) {
            if (in_array($row['date'], $existingDates, true)) {
                continue;
            }
            $toInsert[] = [
                'bird_batch_id' => $batchId,
                'date' => $row['date'],
                'eggs_collected' => $row['eggs_collected'],
                'cracked_or_damaged' => $row['cracked_or_damaged'],
                'eggs_used_internal' => $row['eggs_used_internal'],
                'notes' => $row['notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if (empty($toInsert)) {
            return redirect()->route('egg-productions.bulk-import')
                ->withInput()
                ->with('info', 'All ' . count($parsed) . ' dates already have records for this batch. No new records added.');
        }
        EggProduction::insert($toInsert);
        $count = count($toInsert);
        return redirect()->route('egg-productions.index')
            ->with('success', "Bulk import complete: {$count} egg production record(s) added.");
    }

    public function index(Request $request)
    {
        $sort = $request->query('sort', 'date');
        $direction = strtolower($request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['date', 'batch', 'farm', 'eggs_collected', 'cracked_or_damaged', 'eggs_used_internal'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'date';
        }

        $query = EggProduction::query()->with('birdBatch.farm');
        switch ($sort) {
            case 'date':
                $query->orderBy('date', $direction);
                break;
            case 'eggs_collected':
            case 'cracked_or_damaged':
            case 'eggs_used_internal':
                $query->orderBy($sort, $direction);
                break;
            case 'batch':
                $query->leftJoin('bird_batches', 'egg_productions.bird_batch_id', '=', 'bird_batches.id')
                    ->select('egg_productions.*')
                    ->orderBy('bird_batches.batch_code', $direction);
                break;
            case 'farm':
                $query->leftJoin('bird_batches as bb', 'egg_productions.bird_batch_id', '=', 'bb.id')
                    ->leftJoin('farms', 'bb.farm_id', '=', 'farms.id')
                    ->select('egg_productions.*')
                    ->orderBy('farms.name', $direction);
                break;
        }

        $productions = $query->paginate(50)->withQueryString();
        return view('egg-productions.index', compact('productions', 'sort', 'direction'));
    }

    /**
     * Return all egg production records as JSON for "Show all" (jQuery fetch).
     */
    public function data()
    {
        $productions = EggProduction::with('birdBatch.farm')->latest('date')->get();
        return response()->json($productions);
    }

    public function create()
    {
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->get();
        return view('egg-productions.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'eggs_collected' => 'required|integer|min:0',
            'cracked_or_damaged' => 'required|integer|min:0',
            'eggs_used_internal' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $record = EggProduction::create($data);

        app(CrudNotificationService::class)->notify('egg_production', 'created', $record, auth()->user());

        return redirect()->route('egg-productions.index')->with('success', 'Egg production record created successfully.');
    }

    public function show(EggProduction $eggProduction)
    {
        $eggProduction->load('birdBatch.farm');
        return view('egg-productions.show', compact('eggProduction'));
    }

    public function edit(EggProduction $eggProduction)
    {
        $batches = BirdBatch::whereIn('purpose', ['egg_production', 'layer'])
            ->with('farm')
            ->get();
        return view('egg-productions.edit', compact('eggProduction', 'batches'));
    }

    public function update(Request $request, EggProduction $eggProduction)
    {
        $data = $request->validate([
            'bird_batch_id' => 'required|exists:bird_batches,id',
            'date' => 'required|date',
            'eggs_collected' => 'required|integer|min:0',
            'cracked_or_damaged' => 'required|integer|min:0',
            'eggs_used_internal' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $eggProduction->update($data);

        app(CrudNotificationService::class)->notify('egg_production', 'updated', $eggProduction, auth()->user());

        return redirect()->route('egg-productions.index')->with('success', 'Egg production record updated successfully.');
    }

    public function destroy(EggProduction $eggProduction)
    {
        $recordCopy = clone $eggProduction;
        $eggProduction->delete();

        app(CrudNotificationService::class)->notify('egg_production', 'deleted', $recordCopy, auth()->user());

        return redirect()->route('egg-productions.index')->with('success', 'Egg production record deleted successfully.');
    }
}
