<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileDocument;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf',
            'report' => 'required|file|mimes:pdf',
        ]);

        // Simpan file ke disk PUBLIC
        $cv = $request->file('cv')->store('documents', 'public');
        $report = $request->file('report')->store('documents', 'public');

        // Simpan path ke DB
        $cvDoc = FileDocument::create([
            'type' => 'cv',
            'path' => $cv       // contoh: documents/abcd.pdf
        ]);

        $reportDoc = FileDocument::create([
            'type' => 'report',
            'path' => $report
        ]);

        return response()->json([
            'cv_id' => $cvDoc->id,
            'report_id' => $reportDoc->id
        ]);
    }
}
