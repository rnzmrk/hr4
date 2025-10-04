<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenefitPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // HMO, SSS, PhilHealth, etc.
        'rate_type', // monthly, fixed
        'employee_share', // amount per month
        'employer_share', // amount per month
        'active',
    ];

    protected $casts = [
        'employee_share' => 'decimal:2',
        'employer_share' => 'decimal:2',
        'active' => 'boolean',
    ];
}
