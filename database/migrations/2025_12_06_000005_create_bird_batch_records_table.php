<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bird_batch_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_batch_id')->constrained('bird_batches')->cascadeOnDelete();
            $table->date('record_date');
            $table->decimal('feed_used_kg', 12, 2)->default(0);
            $table->decimal('water_used_litres', 12, 2)->nullable();
            $table->integer('mortality_count')->default(0);
            $table->integer('cull_count')->default(0);
            $table->decimal('average_weight_kg', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bird_batch_records');
    }
};