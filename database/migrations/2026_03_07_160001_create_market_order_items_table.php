<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_order_id')->constrained('market_orders')->cascadeOnDelete();
            $table->string('unit_type', 20); // crate, piece
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_order_items');
    }
};
