<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_batch_id')->constrained('bird_batches')->cascadeOnDelete();
            $table->date('date');
            $table->string('medication_name');
            $table->string('dosage')->nullable();
            $table->decimal('quantity_used', 12, 2)->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_records');
    }
};