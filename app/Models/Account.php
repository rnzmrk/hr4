<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'role',
        'account_type',
        'department',
        'status',
        'password_hashed',
        'password_plain',
        'blocked',
    ];

    protected $casts = [
        'blocked' => 'boolean',
    ];
}
