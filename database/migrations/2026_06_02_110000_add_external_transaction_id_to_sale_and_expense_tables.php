<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['egg_client_sales', 'egg_sales', 'bird_sales', 'crop_sales', 'crop_input_expenses'];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'external_transaction_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->string('external_transaction_id', 64)->nullable()->index();
            });
        }

        if (Schema::hasTable('egg_client_sales') && Schema::hasColumn('egg_client_sales', 'external_transaction_id')) {
            DB::table('egg_client_sales')
                ->where('amount_paid', '>', 0)
                ->whereNull('external_transaction_id')
                ->orderBy('id')
                ->lazyById()
                ->each(function ($sale) {
                    DB::table('egg_client_sales')
                        ->where('id', $sale->id)
                        ->update(['external_transaction_id' => 'agri_egg_client_sale_' . $sale->id]);
                });
        }

        if (Schema::hasTable('poultry_expenses') && Schema::hasColumn('poultry_expenses', 'external_transaction_id')) {
            DB::table('poultry_expenses')
                ->whereNull('external_transaction_id')
                ->whereNotNull('priority_bank_synced_at')
                ->orderBy('id')
                ->lazyById()
                ->each(function ($expense) {
                    DB::table('poultry_expenses')
                        ->where('id', $expense->id)
                        ->update(['external_transaction_id' => 'agri_poultry_expense_' . $expense->id]);
                });
        }
    }

    public function down(): void
    {
        $tables = ['egg_client_sales', 'egg_sales', 'bird_sales', 'crop_sales', 'crop_input_expenses'];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'external_transaction_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('external_transaction_id');
            });
        }
    }
};
