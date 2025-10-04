<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('leave_type')->default('unpaid');
            $table->boolean('is_paid')->default(false);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('hours', 8, 2)->nullable();
            $table->string('status')->default('approved');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_records');
    }
};
