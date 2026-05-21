<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnosis;
use App\Models\DiagnosisImage;
use App\Models\ConsultationRequest;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Auth;

class DiagnosisController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'consultation_req_id' => 'required',
            'result' => 'required',
            'notes' => 'nullable',
            'image' => 'nullable|image'
        ]);

        $diagnosis = Diagnosis::create([
            'consultation_req_id' => $request->consultation_req_id,
            'result' => $request->result,
            'notes' => $request->notes,
        ]);

        // upload gambar
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('diagnosis_images', 'public');

            DiagnosisImage::create([
                'diagnosis_id' => $diagnosis->id,
                'image_path' => $path,
            ]);
        }
        
        // Log consultation uploaded (diagnosis result)
        $consultation = ConsultationRequest::findOrFail($request->consultation_req_id);
        $doctor = Auth::user()->doctor;
        if ($doctor) {
            ActivityLogger::logConsultationUploaded($diagnosis, $doctor);
        }

        return back()->with('success', 'Diagnosis berhasil disimpan');
    }
}