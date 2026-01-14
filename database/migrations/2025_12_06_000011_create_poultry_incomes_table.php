<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poultry_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_batch_id')->nullable()->constrained('bird_batches')->cascadeOnDelete();
            $table->string('source');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poultry_incomes');
    }
};