<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('poultry_expenses', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('farm_id')->constrained('expense_categories')->nullOnDelete();
            $table->string('category')->nullable()->change(); // Keep old category field for backward compatibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poultry_expenses', function (Blueprint $table) {
            //
        });
    }
};
