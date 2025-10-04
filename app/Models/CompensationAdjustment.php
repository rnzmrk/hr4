<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompensationAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'effective_date',
        'applied_rate_type', // hourly|monthly
        'adjustment_type', // set|increase|decrease
        'value', // numeric amount applied based on type
        'reason',
        'status', // approved|pending|rejected
    ];

    protected $casts = [
        'effective_date' => 'date',
        'value' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
