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
        Schema::create('medication_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bird_batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('medication_calendar_id')->constrained()->onDelete('cascade');
            $table->date('start_date'); // When the batch arrived/medication schedule starts
            $table->integer('week_number'); // Week number in the schedule (1, 2, 3, etc.)
            $table->string('medication_name');
            $table->text('description')->nullable();
            $table->string('dosage')->nullable();
            $table->string('method')->nullable(); // e.g., "Water", "Feed", "Injection"
            $table->date('scheduled_date'); // When this medication should be given
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->index(['bird_batch_id', 'scheduled_date']);
            $table->index('scheduled_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_schedules');
    }
};
