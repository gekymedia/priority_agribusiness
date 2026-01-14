<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bird_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->string('batch_code');
            $table->string('breed')->nullable();
            $table->string('purpose');
            $table->date('arrival_date');
            $table->integer('quantity_arrived');
            $table->decimal('cost_per_bird', 12, 2)->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bird_batches');
    }
};