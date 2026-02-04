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
        Schema::table('payrolls', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('payrolls', 'employee_name')) {
                $table->string('employee_name')->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'position')) {
                $table->string('position')->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'department')) {
                $table->string('department')->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'salary')) {
                $table->decimal('salary', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'sss')) {
                $table->decimal('sss', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'pagibig')) {
                $table->decimal('pagibig', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'philhealth')) {
                $table->decimal('philhealth', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'income_tax')) {
                $table->decimal('income_tax', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'incentives')) {
                $table->decimal('incentives', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'pay_date')) {
                $table->date('pay_date')->nullable();
            }
            
            // Add foreign key if not exists
            if (!Schema::hasColumn('payrolls', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $columns = [
                'employee_name',
                'position', 
                'department',
                'salary',
                'sss',
                'pagibig',
                'philhealth',
                'income_tax',
                'incentives',
                'pay_date'
            ];
            
            // Only drop columns that exist
            foreach ($columns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
