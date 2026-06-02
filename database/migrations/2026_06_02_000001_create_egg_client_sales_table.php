<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egg_client_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_batch_id')->constrained('bird_batches')->cascadeOnDelete();
            $table->date('date');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_contact')->nullable();
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egg_client_sales');
    }
};
