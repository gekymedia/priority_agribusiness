<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('event');
            $table->boolean('enabled')->default(true);
            $table->json('channels')->nullable();
            $table->timestamps();

            $table->unique(['module', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
