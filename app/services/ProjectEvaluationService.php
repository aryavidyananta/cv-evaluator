<?php

namespace App\Services;

class ProjectEvaluationService
{
    public function __construct(private LlmService $llm) {}

    public function evaluate(string $reportText, string $context): array
    {
        $reportText = $this->truncate($reportText, 3000);

        $prompt = <<<EOT
Anda adalah evaluator laporan project backend.

Gunakan rubrik berikut sebagai acuan:
$context

TUGAS:
1. Berikan nilai "score" (1–5)
2. Berikan "feedback" singkat dan jelas

FORMAT WAJIB JSON TANPA TEKS DI LUAR JSON:
{
  "score": 4,
  "feedback": "string"
}

LAPORAN PROJECT:
$reportText
EOT;

        try {
            return $this->llm->askJson($prompt);
        } catch (\Exception $e) {
            return [
                'score' => 3,
                'feedback' => "Gagal evaluasi: " . $e->getMessage()
            ];
        }
    }

    private function truncate(string $text, int $len): string
    {
        return strlen($text) <= $len
            ? $text
            : substr($text, 0, $len) . '...';
    }
}
