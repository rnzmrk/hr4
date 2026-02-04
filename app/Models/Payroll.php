<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_name',
        'position',
        'department',
        'salary',
        'sss',
        'pagibig',
        'philhealth',
        'income_tax',
        'incentives',
        'net_pay',
        'pay_date',
        'period_start',
        'period_end',
        'role',
        'hours_worked',
        'rate_type',
        'rate',
        'gross_pay',
        'deductions',
        'status',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'sss' => 'decimal:2',
        'pagibig' => 'decimal:2',
        'philhealth' => 'decimal:2',
        'income_tax' => 'decimal:2',
        'incentives' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'pay_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'hours_worked' => 'decimal:2',
        'rate' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'deductions' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get formatted salary
     */
    public function getFormattedSalaryAttribute()
    {
        return '₱' . number_format($this->salary, 2);
    }

    /**
     * Get formatted SSS
     */
    public function getFormattedSssAttribute()
    {
        return '₱' . number_format($this->sss, 2);
    }

    /**
     * Get formatted PhilHealth
     */
    public function getFormattedPhilhealthAttribute()
    {
        return '₱' . number_format($this->philhealth, 2);
    }

    /**
     * Get formatted Pag-IBIG
     */
    public function getFormattedPagibigAttribute()
    {
        return '₱' . number_format($this->pagibig, 2);
    }

    /**
     * Get formatted Income Tax
     */
    public function getFormattedIncomeTaxAttribute()
    {
        return '₱' . number_format($this->income_tax, 2);
    }

    /**
     * Get formatted Net Pay
     */
    public function getFormattedNetPayAttribute()
    {
        return '₱' . number_format($this->net_pay, 2);
    }

    /**
     * Get formatted Incentives
     */
    public function getFormattedIncentivesAttribute()
    {
        return '₱' . number_format($this->incentives, 2);
    }
}
