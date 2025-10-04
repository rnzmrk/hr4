<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requested_by');
            $table->string('department');
            $table->string('title');
            $table->unsignedInteger('openings')->default(1);
            $table->string('status')->default('Draft');
            $table->string('type')->nullable();
            $table->string('arrangement')->nullable();
            $table->text('description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('qualifications')->nullable();
            $table->timestamps();
            $table->index(['department', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
