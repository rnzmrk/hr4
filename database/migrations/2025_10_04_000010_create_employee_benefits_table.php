<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('reward_id');
            $table->timestamps();
            $table->unique(['employee_id', 'reward_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_benefits');
    }
};
