<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poultry_expense_bird_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poultry_expense_id')->constrained('poultry_expenses')->cascadeOnDelete();
            $table->foreignId('bird_batch_id')->constrained('bird_batches')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['poultry_expense_id', 'bird_batch_id'], 'poultry_expense_batch_unique');
        });

        // Backfill existing single-batch links into the pivot.
        $rows = DB::table('poultry_expenses')
            ->whereNotNull('bird_batch_id')
            ->select('id as poultry_expense_id', 'bird_batch_id', 'created_at', 'updated_at')
            ->get();

        $now = now();
        foreach ($rows->chunk(200) as $chunk) {
            $insert = [];
            foreach ($chunk as $row) {
                $insert[] = [
                    'poultry_expense_id' => $row->poultry_expense_id,
                    'bird_batch_id' => $row->bird_batch_id,
                    'created_at' => $row->created_at ?? $now,
                    'updated_at' => $row->updated_at ?? $now,
                ];
            }
            if ($insert !== []) {
                DB::table('poultry_expense_bird_batch')->insert($insert);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('poultry_expense_bird_batch');
    }
};
