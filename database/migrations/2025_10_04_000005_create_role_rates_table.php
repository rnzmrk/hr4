<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_rates', function (Blueprint $table) {
            $table->id();
            $table->string('role')->index();
            $table->enum('rate_type', ['hourly', 'monthly'])->default('monthly');
            $table->decimal('rate', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_rates');
    }
};
