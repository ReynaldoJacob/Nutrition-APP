<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\PatientProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {        $request->validate([
            'patient_id'  => 'required|integer|exists:users,id',
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'type'        => 'required|in:initial,follow_up,emergency,online',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->start_time);
        $endAt       = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->end_time);
        $duration    = (int) $scheduledAt->diffInMinutes($endAt);

        Appointment::create([
            'patient_id'      => $request->patient_id,
            'nutritionist_id' => $request->user()->id,
            'scheduled_at'    => $scheduledAt,
            'duration'        => $duration,
            'type'            => $request->type,
            'status'          => 'scheduled',
            'notes'           => $request->notes,
        ]);

        return redirect()->back();
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        abort_if($appointment->nutritionist_id !== $request->user()->id, 403);

        if ($appointment->status === 'cancelled') {
            return redirect()->back()->withErrors(['status' => 'Esta cita ya fue cancelada previamente.']);
        }

        $request->validate([
            'cancelled_reason' => 'nullable|string|max:255',
        ]);

        $appointment->update([
            'status'           => 'cancelled',
            'cancelled_reason' => $request->cancelled_reason,
            'cancelled_by_id'  => $request->user()->id,
        ]);

        return redirect()->back();
    }

    public function start(Request $request, Appointment $appointment)
    {
        abort_if($appointment->nutritionist_id !== $request->user()->id, 403);
        abort_if(in_array($appointment->status, ['cancelled', 'completed']), 422);

        // Marcar como confirmada si no lo estaba
        if ($appointment->status !== 'confirmed') {
            $appointment->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        }

        $patient = $appointment->patient;
        $profile = PatientProfile::where('user_id', $patient->id)->first();

        // Última cita completada para mostrar datos de la visita anterior
        $previousAppointment = Appointment::where('patient_id', $patient->id)
            ->where('status', 'completed')
            ->where('id', '!=', $appointment->id)
            ->latest('scheduled_at')
            ->first();

        return Inertia::render('ActiveConsultation', [
            'appointment' => [
                'id'          => $appointment->id,
                'type'        => $appointment->type,
                'typeLabel'   => Appointment::TYPE_LABELS[$appointment->type] ?? $appointment->type,
                'scheduledAt' => $appointment->scheduled_at->toIso8601String(),
                'duration'    => $appointment->duration,
                'notes'       => $appointment->notes,
                'startedAt'   => now()->toIso8601String(),
            ],
            'patient' => [
                'id'     => $patient->id,
                'name'   => $patient->full_name,
                'avatar' => $patient->avatar,
                'code'   => '#CS-' . str_pad($profile?->id ?? $patient->id, 4, '0', STR_PAD_LEFT),
                'height' => $profile?->height,
                'bloodType' => $profile?->blood_type,
                'allergies' => $profile?->allergies ?? [],
                'medicalConditions' => $profile?->medical_conditions ?? [],
                'currentWeight' => $profile?->current_weight,
                'goalWeight'    => $profile?->goal_weight,
            ],
            'previousVisit' => $previousAppointment ? [
                'date'         => $previousAppointment->scheduled_at->toIso8601String(),
                'weight'       => $previousAppointment->weight_at_visit,
                'summary'      => $previousAppointment->summary,
            ] : null,
        ]);
    }

    public function finish(Request $request, Appointment $appointment)
    {
        abort_if($appointment->nutritionist_id !== $request->user()->id, 403);

        $request->validate([
            'weight'      => 'nullable|numeric|min:1|max:500',
            'body_fat'    => 'nullable|numeric|min:1|max:100',
            'muscle_mass' => 'nullable|numeric|min:1|max:300',
            'waist'       => 'nullable|numeric|min:1|max:300',
            'hip'         => 'nullable|numeric|min:1|max:300',
            'blood_pressure' => 'nullable|string|max:20',
            'summary'     => 'nullable|string|max:5000',
        ]);

        $appointment->update([
            'status'        => 'completed',
            'completed_at'  => now(),
            'weight_at_visit' => $request->weight,
            'summary'       => $request->summary,
        ]);

        // Actualizar peso actual en el perfil
        if ($request->weight) {
            PatientProfile::where('user_id', $appointment->patient_id)->update([
                'current_weight'    => $request->weight,
                'last_consultation' => now(),
            ]);
        }

        return redirect()->route('calendario');
    }
}
