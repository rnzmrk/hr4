<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type', // vacation, sick, etc.
        'is_paid',
        'start_date',
        'end_date',
        'hours', // optional for partial days
        'status', // approved|pending|rejected
        'notes',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'hours' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
