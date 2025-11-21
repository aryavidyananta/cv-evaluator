<?php

use Illuminate\Support\Facades\Route;
use App\Services\LlmService;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\EvaluateController;
use App\Http\Controllers\ResultController;

Route::get('/test-openrouter', function (LlmService $llm) {
    try {
        $result = $llm->ask("Halo, balas dengan 1 kalimat.");
        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::post('/upload', [UploadController::class, 'upload']);
Route::post('/evaluate', [EvaluateController::class, 'evaluate']);
Route::get('/result/{id}', [ResultController::class, 'show']);
