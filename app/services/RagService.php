<?php

namespace App\Services;

class RagService
{
    private string $basePath;

    public function __construct()
    {
        // path lengkap ke folder app/rag/docs
        $this->basePath = base_path('app/rag/docs');
    }

    public function retrieveForCV(): string
    {
        $jobDesc      = file_get_contents($this->basePath . '/job_description.txt');
        $cvRubric     = file_get_contents($this->basePath . '/cv_rubric.txt');

        return $jobDesc . "\n\n" . $cvRubric;
    }

    public function retrieveForReport(): string
    {
        $brief        = file_get_contents($this->basePath . '/case_study_brief.txt');
        $projectRubric = file_get_contents($this->basePath . '/project_rubric.txt');

        return $brief . "\n\n" . $projectRubric;
    }
}
