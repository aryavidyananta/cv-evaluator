<?php

namespace App\Jobs;

use App\Models\EvaluationJob;
use App\Models\FileDocument;
use App\Services\PdfReaderService;
use App\Services\RagService;
use App\Services\CvEvaluationService;
use App\Services\ProjectEvaluationService;
use App\Services\LlmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RunEvaluationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public $jobId) {}

    public function handle(
        PdfReaderService $pdfReader,
        RagService $rag,
        CvEvaluationService $cvEval,
        ProjectEvaluationService $projectEval,
        LlmService $llm
    ) {
        Log::info("=== RunEvaluationJob START for job_id={$this->jobId} ===");

        $job = EvaluationJob::find($this->jobId);
        Log::info("Job found", $job->toArray());

        $job->update(['status' => 'processing']);
        Log::info("Job status updated to processing");

        $cv = FileDocument::find($job->cv_id);
        $report = FileDocument::find($job->report_id);
        Log::info("Loaded CV and Report documents", [
            'cv_path' => $cv->path,
            'report_path' => $report->path
        ]);

        $cvText = $pdfReader->read($cv->path);
        $reportText = $pdfReader->read($report->path);
        Log::info("PDF reading completed", [
            'cv_text_length' => strlen($cvText),
            'report_text_length' => strlen($reportText)
        ]);

        $cvContext = $rag->retrieveForCV();
        $reportContext = $rag->retrieveForReport();
        Log::info("RAG Context loaded", [
            'cv_context_length' => strlen($cvContext),
            'report_context_length' => strlen($reportContext)
        ]);

        $cvResult = $cvEval->evaluate($cvText, $cvContext);
        Log::info("CV Evaluation completed", $cvResult);

        $reportResult = $projectEval->evaluate($reportText, $reportContext);
        Log::info("Project Evaluation completed", $reportResult);

        $summary = $llm->ask("Ringkas hasil evaluasi berikut:\n\n" . json_encode([
            'cv' => $cvResult,
            'report' => $reportResult
        ]));
        Log::info("Summary generated");

        $job->update([
            'cv_match_rate'    => $cvResult['match_rate'],
            'cv_feedback'      => $cvResult['feedback'],
            'project_score'    => $reportResult['score'],
            'project_feedback' => $reportResult['feedback'],
            'overall_summary'  => $summary,
            'status'           => 'completed'
        ]);

        Log::info("Job updated to completed");
        Log::info("=== RunEvaluationJob DONE for job_id={$this->jobId} ===");
    }
}
