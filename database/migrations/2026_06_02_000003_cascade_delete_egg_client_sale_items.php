<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('egg_sales', function (Blueprint $table) {
            $table->dropForeign(['egg_client_sale_id']);
            $table->foreign('egg_client_sale_id')
                ->references('id')
                ->on('egg_client_sales')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('egg_sales', function (Blueprint $table) {
            $table->dropForeign(['egg_client_sale_id']);
            $table->foreign('egg_client_sale_id')
                ->references('id')
                ->on('egg_client_sales')
                ->nullOnDelete();
        });
    }
};
