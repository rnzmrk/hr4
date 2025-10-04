<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('benefit_plan_id')->constrained('benefit_plans')->cascadeOnDelete();
            $table->decimal('employee_share_override', 12, 2)->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'benefit_plan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_benefits');
    }
};
