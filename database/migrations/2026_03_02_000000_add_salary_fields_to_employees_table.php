<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('base_salary', 12, 2)->default(0)->after('notes');
            $table->json('allowances')->nullable()->after('base_salary');
            $table->string('bank_name')->nullable()->after('allowances');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
            $table->string('bank_branch')->nullable()->after('bank_account_number');
            $table->string('bank_swift_or_sort')->nullable()->after('bank_branch');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'base_salary',
                'allowances',
                'bank_name',
                'bank_account_name',
                'bank_account_number',
                'bank_branch',
                'bank_swift_or_sort',
            ]);
        });
    }
};
