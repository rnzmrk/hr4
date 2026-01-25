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
        Schema::table('accounts', function (Blueprint $table) {
            // Drop old password columns
            $table->dropColumn(['password_hashed', 'password_plain']);
            
            // Add new password column for plain text storage
            $table->string('password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Drop the new password column
            $table->dropColumn('password');
            
            // Add back the old columns
            $table->string('password_hashed')->nullable();
            $table->string('password_plain')->nullable();
        });
    }
};
