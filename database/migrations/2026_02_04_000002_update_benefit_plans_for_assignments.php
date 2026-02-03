<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('benefit_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('benefit_plans', 'employee_id')) {
                $table->foreignId('employee_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('employees')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('benefit_plans', 'assigned_date')) {
                $table->date('assigned_date')->nullable()->after('type');
            }

            if (Schema::hasColumn('benefit_plans', 'employee_share')) {
                $table->dropColumn('employee_share');
            }
        });
    }

    public function down(): void
    {
        Schema::table('benefit_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('benefit_plans', 'employee_share')) {
                $table->decimal('employee_share', 12, 2)->default(0)->after('rate_type');
            }

            if (Schema::hasColumn('benefit_plans', 'assigned_date')) {
                $table->dropColumn('assigned_date');
            }

            if (Schema::hasColumn('benefit_plans', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }
        });
    }
};
