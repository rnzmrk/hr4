<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('accepted_contracts');
    }

    public function down(): void
    {
        // Recreate minimal table structure if rolled back
        Schema::create('accepted_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('department')->nullable();
            $table->string('role')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamps();
        });
    }
};
