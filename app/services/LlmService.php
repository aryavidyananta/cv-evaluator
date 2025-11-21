<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class LlmService
{
    public function ask(string $prompt): string
    {
        return $this->callOpenRouter($prompt);
    }

    private function callOpenRouter(string $prompt, int $timeout = 40): string
    {
        $endpoint = config('services.openrouter.endpoint') . '/chat/completions';

        try {

            $response = Http::timeout($timeout)
                ->retry(2, 1500)
                ->withHeaders([
                    "Authorization" => "Bearer " . config('services.openrouter.key'),
                    "x-title" => "Backend Service",
                    "HTTP-Referer" => "http://localhost",
                ])
                ->post($endpoint, [
                    "model" => config('services.openrouter.model'),
                    "messages" => [
                        [
                            "role" => "user",
                            "content" => $prompt
                        ]
                    ],
                    "temperature" => 0.2,
                    "max_tokens" => 1200
                ]);

            if ($response->failed()) {
                throw new \Exception(
                    "OpenRouter error: " . $response->body()
                );
            }

            return $response->json("choices.0.message.content");

        } catch (ConnectionException $e) {
            throw new \Exception("Timeout saat menghubungi OpenRouter.");
        }
    }

    /**
     * Ask JSON but WITHOUT json_mode (karena FREE model tidak support)
     */
    public function askJson(string $prompt): array
    {
        $response = $this->ask($prompt);

        return $this->extractJson($response);
    }

    /**
     * Ambil JSON dari teks model
     */
    private function extractJson(string $text): array
    {
        // ambil { ... }
        preg_match('/\{.*\}/s', $text, $match);

        if (!$match) {
            throw new \Exception("Model tidak mengembalikan JSON yang valid.");
        }

        $json = $match[0];

        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON tidak valid: " . json_last_error_msg());
        }

        return $decoded;
    }
}
