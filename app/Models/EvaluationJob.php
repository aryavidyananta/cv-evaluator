<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationJob extends Model
{
    protected $fillable = [
        'job_title',
        'cv_id',
        'report_id',
        'status',
        'cv_match_rate',
        'cv_feedback',
        'project_score',
        'project_feedback',
        'overall_summary'
    ];
}
