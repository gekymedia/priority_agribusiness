<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('pay_period');
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('allowances_total', 12, 2)->default(0);
            $table->decimal('deductions_total', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'pay_period']);
            $table->index(['pay_period', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
