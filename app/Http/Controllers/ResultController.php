<?php

namespace App\Http\Controllers;

use App\Models\EvaluationJob;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function show($id)
    {
        $job = EvaluationJob::findOrFail($id);

        if ($job->status !== 'completed') {
            return response()->json([
                'id' => $job->id,
                'status' => $job->status
            ]);
        }

        return response()->json([
            'id' => $job->id,
            'status' => 'completed',
            'result' => [
                'cv_match_rate' => $job->cv_match_rate,
                'cv_feedback' => $job->cv_feedback,
                'project_score' => $job->project_score,
                'project_feedback' => $job->project_feedback,
                'overall_summary' => $job->overall_summary
            ]
        ]);
    }
}
