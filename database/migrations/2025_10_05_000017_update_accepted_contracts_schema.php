<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accepted_contracts', function (Blueprint $table) {
            // Add new columns if missing
            if (!Schema::hasColumn('accepted_contracts', 'candidate_id')) $table->unsignedBigInteger('candidate_id')->nullable()->index();
            if (!Schema::hasColumn('accepted_contracts', 'offer_date')) $table->date('offer_date')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'offer_status')) $table->string('offer_status', 50)->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_job_title')) $table->string('candidate_job_title')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_last_name')) $table->string('candidate_last_name')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_first_name')) $table->string('candidate_first_name')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_middle_name')) $table->string('candidate_middle_name')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_suffix_name')) $table->string('candidate_suffix_name')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_address')) $table->string('candidate_address')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_email')) $table->string('candidate_email')->nullable()->index();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_phone')) $table->string('candidate_phone', 50)->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_age')) $table->unsignedInteger('candidate_age')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_gender')) $table->string('candidate_gender', 50)->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_birth_date')) $table->date('candidate_birth_date')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'candidate_civil_status')) $table->string('candidate_civil_status', 50)->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'skills')) $table->text('skills')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'experience')) $table->text('experience')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'education')) $table->text('education')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'interviewDate')) $table->date('interviewDate')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'interviewTime')) $table->string('interviewTime')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'status')) $table->string('status', 50)->nullable();

            // Drop legacy columns if they exist
            foreach (['external_id','name','email','department','role','start_date'] as $col) {
                if (Schema::hasColumn('accepted_contracts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('accepted_contracts', function (Blueprint $table) {
            // Recreate legacy columns if needed
            if (!Schema::hasColumn('accepted_contracts', 'external_id')) $table->string('external_id')->nullable()->index();
            if (!Schema::hasColumn('accepted_contracts', 'name')) $table->string('name')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'email')) $table->string('email')->nullable()->index();
            if (!Schema::hasColumn('accepted_contracts', 'department')) $table->string('department')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'role')) $table->string('role')->nullable();
            if (!Schema::hasColumn('accepted_contracts', 'start_date')) $table->date('start_date')->nullable();

            // Drop new columns on rollback
            $cols = [
                'candidate_id','offer_date','offer_status','candidate_job_title','candidate_last_name','candidate_first_name',
                'candidate_middle_name','candidate_suffix_name','candidate_address','candidate_email','candidate_phone',
                'candidate_age','candidate_gender','candidate_birth_date','candidate_civil_status','skills','experience',
                'education','interviewDate','interviewTime','status'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('accepted_contracts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
