<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'first_name',
        'middle_name',
        'last_name',
        'suffix_name',
        'address',
        'phone',
        'age',
        'gender',
        'birth_date',
        'civil_status',
        'skills',
        'experience',
        'education',
        'position',
        'date_hired',
        'employee_status',
        'email',
        'department_id',
        'salary',
        'atm_number',
        'role',
        'profile',
    ];

    protected $casts = [
        'start_date' => 'date',
        'birth_date' => 'date',
        'date_hired' => 'date',
        'salary' => 'integer',
    ];

    protected $appends = ['calculated_age'];

    public function getCalculatedAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->diffInYears(now()) : null;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function benefitPlans()
    {
        return $this->hasMany(BenefitPlan::class);
    }
}
