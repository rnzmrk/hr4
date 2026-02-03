<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $benefitPlanForeign = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'benefit_plan_id')
            ->where('CONSTRAINT_NAME', 'like', '%foreign%')
            ->value('CONSTRAINT_NAME');
        $hasBenefitPlanId = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'benefit_plan_id')
            ->exists();
        $hasEmployeeShareOverride = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'employee_share_override')
            ->exists();
        $hasRewardId = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'reward_id')
            ->exists();

        if ($hasBenefitPlanId) {
            if ($benefitPlanForeign) {
                try {
                    DB::statement("ALTER TABLE employee_benefits DROP FOREIGN KEY {$benefitPlanForeign}");
                } catch (\Throwable $e) {
                    // ignore missing foreign key
                }
            }
            try {
                DB::statement('ALTER TABLE employee_benefits DROP COLUMN benefit_plan_id');
            } catch (\Throwable $e) {
                // ignore missing column
            }
        }

        if ($hasEmployeeShareOverride) {
            try {
                DB::statement('ALTER TABLE employee_benefits DROP COLUMN employee_share_override');
            } catch (\Throwable $e) {
                // ignore missing column
            }
        }

        if (!$hasRewardId) {
            Schema::table('employee_benefits', function (Blueprint $table) {
                $table->foreignId('reward_id')->constrained('rewards')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        $rewardForeign = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'reward_id')
            ->where('CONSTRAINT_NAME', 'like', '%foreign%')
            ->value('CONSTRAINT_NAME');
        $hasRewardId = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'reward_id')
            ->exists();
        $hasBenefitPlanId = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'benefit_plan_id')
            ->exists();
        $hasEmployeeShareOverride = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'employee_benefits')
            ->where('COLUMN_NAME', 'employee_share_override')
            ->exists();

        if ($hasRewardId) {
            if ($rewardForeign) {
                try {
                    DB::statement("ALTER TABLE employee_benefits DROP FOREIGN KEY {$rewardForeign}");
                } catch (\Throwable $e) {
                    // ignore missing foreign key
                }
            }
            try {
                DB::statement('ALTER TABLE employee_benefits DROP COLUMN reward_id');
            } catch (\Throwable $e) {
                // ignore missing column
            }
        }

        Schema::table('employee_benefits', function (Blueprint $table) use ($hasBenefitPlanId, $hasEmployeeShareOverride) {
            if (!$hasBenefitPlanId) {
                $table->foreignId('benefit_plan_id')->constrained('benefit_plans')->cascadeOnDelete();
            }

            if (!$hasEmployeeShareOverride) {
                $table->decimal('employee_share_override', 12, 2)->nullable();
            }
        });
    }
};
