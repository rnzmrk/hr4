<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'openings',
        'employee_count_override',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function requisitions(): HasMany
    {
        return $this->hasMany(Requisition::class);
    }
}
