<?php

namespace App\Http\Controllers;

use App\Models\ConsultationRequest;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;

class DoctorController extends Controller
{
    private function getDoctor()
    {
        $user = Auth::user();

        if (!$user || !$user->doctor) {
            abort(403, 'Doctor not found');
        }

        return $user->doctor;
    }

    private function authorizeDoctor($consultation)
    {
        $doctor = $this->getDoctor();

        if ($consultation->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized');
        }
    }


    public function dashboard(Request $request)
    {
        $doctor = $this->getDoctor();

        // Jumlah konsultasi yang masih menunggu persetujuan
        $pendingCount = ConsultationRequest::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->count();

        // Jumlah konsultasi yang dijadwalkan hari ini (approved & scheduled today)
        $todayScheduleCount = ConsultationRequest::where('doctor_id', $doctor->id)
            ->where('status', 'approved')
            ->whereDate('scheduled_date', Carbon::today())
            ->count();

        // Debug: Log what we're querying for today's date
        $todayDebug = ConsultationRequest::where('doctor_id', $doctor->id)
            ->where('status', 'approved')
            ->get();
        
        \Log::info('Today debug for doctor ' . $doctor->id . ':', [
            'today' => Carbon::today()->format('Y-m-d'),
            'total_approved' => $todayDebug->count(),
            'consultations' => $todayDebug->map(function($c) {
                return [
                    'id' => $c->id,
                    'scheduled_date' => $c->scheduled_date,
                    'scheduled_date_type' => gettype($c->scheduled_date),
                    'status' => $c->status
                ];
            })
        ]);

        // Jumlah pasien unik yang pernah ditangani (semua status selain pending)
        $patientsHandledCount = ConsultationRequest::where('doctor_id', $doctor->id)
            ->whereIn('status', ['approved', 'done', 'rejected'])
            ->distinct('patient_id')
            ->count('patient_id');

        // History: semua konsultasi yang sudah selesai atau ditolak
        $histories = ConsultationRequest::where('doctor_id', $doctor->id)
            ->with(['patient.user'])
            ->whereIn('status', ['done', 'rejected'])
            ->latest()
            ->get();

        return view('doctor.dashboard', compact(
            'pendingCount',
            'todayScheduleCount',
            'patientsHandledCount',
            'histories'
        ));
    }

    public function approve($id)
    {
        $consultation = ConsultationRequest::findOrFail($id);
        $this->authorizeDoctor($consultation);

        $consultation->update(['status' => 'approved']);
        
        // Log consultation approval
        $doctor = $this->getDoctor();
        ActivityLogger::logConsultationApproved($consultation, $doctor);

        return response()->json([
            'message' => 'Consultation approved successfully',
            'status' => $consultation->status
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        $consultation = ConsultationRequest::findOrFail($id);
        $this->authorizeDoctor($consultation);

        $consultation->update(['status' => 'rejected']);
        
        // Log consultation rejection
        $doctor = $this->getDoctor();
        ActivityLogger::logConsultationRejected($consultation, $doctor);

        return response()->json([
            'message' => 'Consultation rejected successfully',
            'status' => $consultation->status
        ]);
    }

    public function schedule(Request $request, $id)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i'
        ]);

        $consultation = ConsultationRequest::findOrFail($id);
        $this->authorizeDoctor($consultation);

        if (!in_array($consultation->status, ['pending', 'approved'])) {
            return response()->json([
                'error' => 'Consultation cannot be scheduled in its current state'
            ], 400);
        }

        $wasAlreadyApproved = $consultation->status === 'approved';

        $consultation->update([
            'status'         => 'approved',
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
        ]);

        // Log approval only if it was still pending
        if (!$wasAlreadyApproved) {
            $doctor = $this->getDoctor();
            ActivityLogger::logConsultationApproved($consultation, $doctor);
        }

        return response()->json([
            'message'        => 'Consultation approved and scheduled successfully',
            'scheduled_date' => $consultation->scheduled_date,
            'scheduled_time' => $consultation->scheduled_time,
        ]);
    }

    public function getConsultationDetails($id)
    {
        $consultation = ConsultationRequest::with('patient')->findOrFail($id);
        $this->authorizeDoctor($consultation);

        return response()->json([
            'id' => $consultation->id,
            'complaint' => $consultation->complaint,
            'status' => $consultation->status,
            'created_at' => $consultation->created_at,
            'scheduled_date' => $consultation->scheduled_date,
            'scheduled_time' => $consultation->scheduled_time,
            'patient' => $consultation->patient ? [
                'id' => $consultation->patient->id,
                'name' => $consultation->patient->name,
                'age' => $consultation->patient->age,
                'contact' => $consultation->patient->contact,
                'gender' => $consultation->patient->gender,
                'address' => $consultation->patient->address,
                'birth_date' => $consultation->patient->birth_date
            ] : null
        ]);
    }

    public function showConsultation($id)
    {
        $consultation = ConsultationRequest::findOrFail($id);
        $this->authorizeDoctor($consultation);

        return view('doctor.consultation-detail', compact('consultation'));
    }

    public function consultations(Request $request)
    {
        $doctor = $this->getDoctor();
        $status = $request->get('status', 'all');

        $query = ConsultationRequest::where('doctor_id', $doctor->id)
            ->with(['patient.user']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $consultations = $query->latest()->get();

        return view('doctor.consultations', compact('consultations', 'status'));
    }

    public function patientsProfile()
    {
        $doctor = $this->getDoctor();

        // Get unique patients who have consultations with this doctor
        $patients = Patient::whereHas('consultations', function ($q) use ($doctor) {
        $q->where('doctor_id', $doctor->id);
        })->with('user')->get();

        return view('doctor.patients-profile', compact('patients'));
    }
}