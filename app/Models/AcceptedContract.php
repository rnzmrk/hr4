<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptedContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'offer_date',
        'offer_status',
        'candidate_job_title',
        'candidate_last_name',
        'candidate_first_name',
        'candidate_middle_name',
        'candidate_suffix_name',
        'candidate_address',
        'candidate_email',
        'candidate_phone',
        'candidate_age',
        'candidate_gender',
        'candidate_birth_date',
        'candidate_civil_status',
        'skills',
        'experience',
        'education',
        'interviewDate',
        'interviewTime',
        'status',
    ];
}
