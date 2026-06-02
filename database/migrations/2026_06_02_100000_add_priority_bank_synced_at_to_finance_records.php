<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'incomes',
            'poultry_expenses',
            'crop_input_expenses',
            'egg_client_sales',
            'egg_sales',
            'bird_sales',
            'crop_sales',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'priority_bank_synced_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->timestamp('priority_bank_synced_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'incomes',
            'poultry_expenses',
            'crop_input_expenses',
            'egg_client_sales',
            'egg_sales',
            'bird_sales',
            'crop_sales',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'priority_bank_synced_at')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('priority_bank_synced_at');
            });
        }
    }
};
