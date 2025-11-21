<?php

namespace App\Services;

class SummaryService
{
    public function summarize(array $cv, array $project): string
    {
        $prompt = <<<EOT
Buat ringkasan evaluasi kandidat dalam 3–5 kalimat berdasarkan:

CV Match Rate: {$cv['match_rate']}
CV Feedback: {$cv['feedback']}

Project Score: {$project['score']}
Project Feedback: {$project['feedback']}

Gunakan bahasa formal, ringkas, dan objektif.
EOT;

        return app(LlmService::class)->ask($prompt);
    }
}
