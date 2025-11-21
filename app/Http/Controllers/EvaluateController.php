<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvaluationJob;
use App\Jobs\RunEvaluationJob;

class EvaluateController extends Controller
{
    public function evaluate(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string',
            'cv_id' => 'required|integer',
            'report_id' => 'required|integer',
        ]);

        $job = EvaluationJob::create([
            'job_title' => $request->job_title,
            'cv_id' => $request->cv_id,
            'report_id' => $request->report_id,
            'status' => 'queued'
        ]);

        dispatch(new RunEvaluationJob($job->id));

        return response()->json([
            'id' => $job->id,
            'status' => 'queued'
        ]);
    }
}
