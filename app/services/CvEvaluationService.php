<?php

namespace App\Services;

class CvEvaluationService
{
    public function __construct(private LlmService $llm) {}

    public function evaluate(string $cvText, string $context): array
    {
        $cvText = $this->truncate($cvText, 2500);

        $prompt = <<<EOT
Anda adalah evaluator CV profesional.
Gunakan konteks berikut sebagai standar penilaian:

KONTEKS:
$context

TUGAS:
1. Hitung "match_rate" antara 0.0 – 1.0
2. Berikan "feedback" singkat, jelas, dan profesional (2–4 kalimat)

FORMAT WAJIB JSON TANPA TEKS DI LUAR JSON:
{
  "match_rate": 0.85,
  "feedback": "string"
}

CV:
$cvText
EOT;

        try {
            return $this->llm->askJson($prompt);
        } catch (\Exception $e) {
            return [
                'match_rate' => 0.4,
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
