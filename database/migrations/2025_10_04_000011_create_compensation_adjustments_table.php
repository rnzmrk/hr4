<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compensation_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('effective_date');
            $table->enum('applied_rate_type', ['hourly', 'monthly'])->default('monthly');
            $table->enum('adjustment_type', ['set', 'increase', 'decrease'])->default('set');
            $table->decimal('value', 12, 2)->default(0);
            $table->string('reason')->nullable();
            $table->string('status')->default('approved');
            $table->timestamps();
            $table->index(['employee_id', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compensation_adjustments');
    }
};
