<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Diagnosis;
use App\Models\ConsultationRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class EarscopeApiController extends Controller
{
    /**
     * Menerima hasil diagnosa dari Flask App (Earscope).
     * Dipanggil secara otomatis setelah stop recording di perangkat earscope.
     *
     * Body: consultation_id (form-data), hasil_diagnosis (form-data),
     *       raw_video (file), processed_video (file)
     */
    public function receive(Request $request)
    {
        Log::info('[Earscope API] Incoming request', [
            'consultation_id' => $request->consultation_id,
            'hasil_diagnosis'  => $request->hasil_diagnosis,
            'has_raw'          => $request->hasFile('raw_video'),
            'has_processed'    => $request->hasFile('processed_video'),
        ]);

        // --- Validasi ---
        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultation_requests,id',
            'hasil_diagnosis'  => 'required|string|max:255',
            'raw_video'        => 'nullable|file|mimes:mp4,avi,mov,mkv,webm|max:204800', // max 200MB
            'processed_video'  => 'nullable|file|mimes:mp4,avi,mov,mkv,webm|max:204800',
        ]);

        $consultationId = $validated['consultation_id'];
        $hasilDiagnosis = $validated['hasil_diagnosis'];

        // --- Pastikan konsultasi berstatus 'scheduled' ---
        $consultation = ConsultationRequest::find($consultationId);
        if (!$consultation || $consultation->status !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'Konsultasi tidak ditemukan atau statusnya bukan scheduled.',
            ], 422);
        }

        // --- Simpan video ke storage permanen ---
        $rawPath       = null;
        $processedPath = null;

        if ($request->hasFile('raw_video')) {
            $rawPath = $request->file('raw_video')
                ->store("earscope_videos/{$consultationId}/raw", 'public');
        }

        if ($request->hasFile('processed_video')) {
            $processedPath = $request->file('processed_video')
                ->store("earscope_videos/{$consultationId}/processed", 'public');
        }

        // --- Buat atau update record Diagnosis ---
        $diagnosis = Diagnosis::updateOrCreate(
            ['consultation_request_id' => $consultationId],
            [
                'ai_result'             => $hasilDiagnosis,
                'raw_video_path'        => $rawPath,
                'processed_video_path'  => $processedPath,
                // Isi diagnosis_result dengan ai_result sebagai default agar field not-null terpenuhi.
                // Dokter masih bisa mengedit/menimpa lewat form web.
                'diagnosis_result'      => $hasilDiagnosis,
            ]
        );

        Log::info('[Earscope API] Diagnosis saved', ['diagnosis_id' => $diagnosis->id]);

        return response()->json([
            'success'      => true,
            'message'      => 'Data earscope berhasil diterima.',
            'diagnosis_id' => $diagnosis->id,
        ], 201);
    }

    /**
     * Mengembalikan data earscope terbaru untuk sebuah konsultasi.
     * Di-polling dari halaman diagnosa dokter.
     */
    public function latest(Request $request)
    {
        $consultationId = $request->query('consultation_id');

        if (!$consultationId) {
            return response()->json(['success' => false, 'message' => 'consultation_id diperlukan.'], 400);
        }

        $diagnosis = Diagnosis::where('consultation_request_id', $consultationId)->first();

        if (!$diagnosis || !$diagnosis->ai_result) {
            return response()->json(['success' => false, 'message' => 'Belum ada data earscope.'], 404);
        }

        return response()->json([
            'success'             => true,
            'ai_result'           => $diagnosis->ai_result,
            'raw_video_url'       => $diagnosis->raw_video_path
                ? Storage::disk('public')->url($diagnosis->raw_video_path)
                : null,
            'processed_video_url' => $diagnosis->processed_video_path
                ? Storage::disk('public')->url($diagnosis->processed_video_path)
                : null,
        ]);
    }
}
