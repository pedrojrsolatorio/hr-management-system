<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Attendance — keep records, nullify employee_id
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->foreignId('employee_id')
                ->nullable()
                ->change()
                ->constrained()
                ->nullOnDelete();
        });

        // Leave requests — keep records, nullify employee_id
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->foreignId('employee_id')
                ->nullable()
                ->change()
                ->constrained()
                ->nullOnDelete();
        });

        // Payrolls — keep records, nullify employee_id
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->foreignId('employee_id')
                ->nullable()
                ->change()
                ->constrained()
                ->nullOnDelete();
        });

        // Performance reviews — keep records, nullify employee_id
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->foreignId('employee_id')
                ->nullable()
                ->change()
                ->constrained()
                ->nullOnDelete();
        });

        // Employees — keep employee row, nullify user_id
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')
                ->nullable()
                ->change()
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->foreignId('employee_id')->change()->constrained()->cascadeOnDelete();
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->foreignId('employee_id')->change()->constrained()->cascadeOnDelete();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->foreignId('employee_id')->change()->constrained()->cascadeOnDelete();
        });

        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->foreignId('employee_id')->change()->constrained()->cascadeOnDelete();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->change()->constrained()->cascadeOnDelete();
        });
    }
};
