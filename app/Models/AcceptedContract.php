<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptedContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'email',
        'department',
        'role',
        'start_date',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];
}
