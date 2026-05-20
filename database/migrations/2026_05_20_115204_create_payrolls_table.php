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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('month'); // format: 2024-01
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('gross_salary', 12, 2);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2);
            $table->string('status')->default('draft'); // draft, approved, paid
            $table->timestamp('paid_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['employee_id', 'month']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
