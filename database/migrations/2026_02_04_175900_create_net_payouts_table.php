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
        Schema::create('net_payouts', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_salary', 12, 2);
            $table->decimal('total_sss', 10, 2);
            $table->decimal('total_pagibig', 10, 2);
            $table->decimal('total_philhealth', 10, 2);
            $table->decimal('total_income_tax', 10, 2);
            $table->decimal('total_net', 12, 2);
            $table->date('date');
            $table->timestamps();
            
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('net_payouts');
    }
};
