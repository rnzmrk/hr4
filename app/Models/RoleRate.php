<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'rate_type', // hourly, monthly
        'rate',
    ];
}
