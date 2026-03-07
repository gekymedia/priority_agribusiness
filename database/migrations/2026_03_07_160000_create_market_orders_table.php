<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('delivery_address')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->boolean('wants_delivery')->default(false);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_gateway', 20)->nullable(); // hubtel, paystack
            $table->string('payment_reference', 100)->nullable();
            $table->string('status', 20)->default('pending'); // pending, paid, delivered, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_orders');
    }
};
