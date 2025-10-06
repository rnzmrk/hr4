<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('role')->nullable();
            $table->date('start_date')->nullable();
            $table->string('status')->default('Active');
            // Extended fields from employee API (normalized)
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('gender', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('civil_status', 50)->nullable();
            $table->text('skills')->nullable();
            $table->text('experience')->nullable();
            $table->text('education')->nullable();
            $table->string('job_title')->nullable();
            $table->date('date_hired')->nullable();
            $table->string('external_employee_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
