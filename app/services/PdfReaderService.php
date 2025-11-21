<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class PdfReaderService
{
    public function read($filePath)
    {
        // Cek apakah path sudah absolut atau relatif
        if (file_exists($filePath)) {
            $fileFullPath = $filePath;
        } else {
            // Coba dari storage/app/public
            $fileFullPath = storage_path("app/public/" . $filePath);
        }

        if (!file_exists($fileFullPath)) {
            throw new \Exception("File tidak ditemukan: $filePath");
        }

        return $this->extractText($fileFullPath);
    }

    private function extractText($fullPath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            
            // Ekstrak semua teks dari PDF
            $text = $pdf->getText();
            
            // Bersihkan whitespace berlebih
            $text = trim(preg_replace('/\s+/', ' ', $text));
            
            if (empty($text)) {
                throw new \Exception("PDF tidak mengandung teks yang dapat diekstrak. Mungkin file berupa gambar/scan.");
            }
            
            return $text;
            
        } catch (\Exception $e) {
            throw new \Exception("Gagal membaca PDF: " . $e->getMessage());
        }
    }
}