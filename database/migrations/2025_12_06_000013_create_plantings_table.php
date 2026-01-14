<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->string('crop_name');
            $table->date('planting_date');
            $table->date('expected_harvest_date')->nullable();
            $table->string('seed_source')->nullable();
            $table->string('quantity_planted')->nullable();
            $table->string('status')->default('growing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantings');
    }
};