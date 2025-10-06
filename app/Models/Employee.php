<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'department_id',
        'role',
        'start_date',
        'status',
        // Extended fields from employee API
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
        'job_title',
        'date_hired',
        'external_employee_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'birth_date' => 'date',
        'date_hired' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
