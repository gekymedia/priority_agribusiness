<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('egg_productions', function (Blueprint $table) {
            $table->boolean('egg_size_breakdown')->default(false)->after('eggs_used_internal');
            $table->unsignedInteger('eggs_large')->default(0)->after('egg_size_breakdown');
            $table->unsignedInteger('eggs_medium')->default(0)->after('eggs_large');
            $table->unsignedInteger('eggs_small')->default(0)->after('eggs_medium');
        });
    }

    public function down(): void
    {
        Schema::table('egg_productions', function (Blueprint $table) {
            $table->dropColumn(['egg_size_breakdown', 'eggs_large', 'eggs_medium', 'eggs_small']);
        });
    }
};
