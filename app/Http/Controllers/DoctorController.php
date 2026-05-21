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

        // stats
        $pendingCount = ConsultationRequest::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->count();

        $todayScheduleCount = ConsultationRequest::where('doctor_id', $doctor->id)
            ->whereDate('scheduled_date', Carbon::today())
            ->count();

        $doneCount = ConsultationRequest::where('doctor_id', $doctor->id)
            ->where('status', 'done')
            ->count();

        // history (all completed consultations)
        $histories = ConsultationRequest::where('doctor_id', $doctor->id)
            ->with(['patient.user'])
            ->whereIn('status', ['done', 'rejected'])
            ->latest()
            ->get();

        return view('doctor.dashboard', compact(
            'pendingCount',
            'todayScheduleCount',
            'doneCount',
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
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|date_format:H:i'
        ]);

        $consultation = ConsultationRequest::findOrFail($id);
        $this->authorizeDoctor($consultation);

        if ($consultation->status !== 'approved') {
            return response()->json([
                'error' => 'Consultation must be approved first'
            ], 400);
        }

        $consultation->update([
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time
        ]);

        return response()->json([
            'message' => 'Consultation scheduled successfully',
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