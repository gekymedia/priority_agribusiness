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
        Schema::create('medication_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Broiler Standard", "Layer Standard", "Custom"
            $table->string('type')->default('broiler'); // broiler, layer, custom
            $table->text('description')->nullable();
            $table->json('schedule'); // JSON array of medication weeks and details
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_calendars');
    }
};
