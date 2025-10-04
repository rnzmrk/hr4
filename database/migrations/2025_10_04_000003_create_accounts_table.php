<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index();
            $table->string('role')->nullable();
            $table->enum('account_type', ['system', 'ess'])->default('system');
            $table->string('department')->nullable();
            $table->string('status')->default('Active');
            $table->string('password_hashed')->nullable();
            $table->boolean('blocked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
