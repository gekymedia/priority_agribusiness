<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egg_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_batch_id')->constrained('bird_batches')->cascadeOnDelete();
            $table->date('date');
            $table->integer('quantity_sold');
            $table->string('unit_type');
            $table->decimal('price_per_unit', 12, 2);
            $table->string('buyer_name')->nullable();
            $table->string('buyer_contact')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egg_sales');
    }
};