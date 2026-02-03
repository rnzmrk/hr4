<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasBenefitPlanId = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'benefit_plan_id')
            ->exists();

        if ($hasBenefitPlanId) {
            $foreignName = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'employee_benefits')
                ->where('COLUMN_NAME', 'benefit_plan_id')
                ->where('CONSTRAINT_NAME', 'like', '%foreign%')
                ->value('CONSTRAINT_NAME');

            if ($foreignName) {
                try {
                    DB::statement("ALTER TABLE employee_benefits DROP FOREIGN KEY {$foreignName}");
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            try {
                DB::statement('ALTER TABLE employee_benefits DROP COLUMN benefit_plan_id');
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('employee_benefits', 'benefit_plan_id')) {
            Schema::table('employee_benefits', function ($table) {
                $table->foreignId('benefit_plan_id')->constrained('benefit_plans')->cascadeOnDelete();
            });
        }
    }
};
