<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('egg_sales', function (Blueprint $table) {
            $table->foreignId('egg_client_sale_id')
                ->nullable()
                ->after('market_order_id')
                ->constrained('egg_client_sales')
                ->nullOnDelete();
            $table->string('egg_size')->nullable()->after('unit_type');
            $table->string('payment_status')->default('paid')->after('price_per_unit');
        });
    }

    public function down(): void
    {
        Schema::table('egg_sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('egg_client_sale_id');
            $table->dropColumn(['egg_size', 'payment_status']);
        });
    }
};
