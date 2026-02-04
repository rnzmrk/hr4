<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetPayout extends Model
{
    protected $fillable = [
        'total_salary',
        'total_sss',
        'total_pagibig',
        'total_philhealth',
        'total_income_tax',
        'total_net',
        'date'
    ];

    protected $casts = [
        'total_salary' => 'decimal:2',
        'total_sss' => 'decimal:2',
        'total_pagibig' => 'decimal:2',
        'total_philhealth' => 'decimal:2',
        'total_income_tax' => 'decimal:2',
        'total_net' => 'decimal:2',
        'date' => 'date'
    ];
}
