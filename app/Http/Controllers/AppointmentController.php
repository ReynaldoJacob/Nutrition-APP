<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ConsultationRecord;
use App\Models\PatientProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    private array $activityFactors = [
        'sedentary'  => 1.2,
        'light'      => 1.375,
        'moderate'   => 1.55,
        'active'     => 1.725,
        'very_active' => 1.9,
    ];

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

        // Último registro del expediente para mostrar datos de la visita anterior
        $previousRecord = ConsultationRecord::where('patient_id', $patient->id)
            ->where('appointment_id', '!=', $appointment->id)
            ->latest('recorded_at')
            ->first();

        // Resumen clínico de la última cita completada
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
                'id'                => $patient->id,
                'name'              => $patient->full_name,
                'avatar'            => $patient->avatar,
                'code'              => '#CS-' . str_pad($profile?->id ?? $patient->id, 4, '0', STR_PAD_LEFT),
                'height'            => $profile?->height,
                'birthDate'         => $patient->birth_date?->toIso8601String(),
                'gender'            => $patient->gender,
                'activityFactor'    => $this->activityFactors[$profile?->activity_level ?? 'sedentary'] ?? 1.375,
                'bloodType'         => $profile?->blood_type,
                'allergies'         => $profile?->allergies ?? [],
                'medicalConditions' => $profile?->medical_conditions ?? [],
                'currentWeight'     => $profile?->current_weight,
                'goalWeight'        => $profile?->goal_weight,
            ],
            'previousVisit' => $previousRecord ? [
                'date'               => $previousRecord->recorded_at->toIso8601String(),
                'weight'             => $previousRecord->weight,
                'bodyFat'            => $previousRecord->body_fat_percentage,
                'muscleMass'         => $previousRecord->muscle_mass,
                'bmi'                => $previousRecord->bmi,
                'waist'              => $previousRecord->waist_cm,
                'hip'                => $previousRecord->hip_cm,
                'bloodPressure'      => $previousRecord->blood_pressure,
                'summary'            => $previousAppointment?->summary,
                // Plan nutricional anterior
                'gebAverage'         => $previousRecord->geb_average,
                'getTotal'           => $previousRecord->get_total,
                'activityFactor'     => $previousRecord->activity_factor,
                'prescriptionType'   => $previousRecord->prescription_type,
                'prescribedKcal'     => $previousRecord->prescribed_kcal,
                'proteinGkg'         => $previousRecord->protein_gkg,
                'fatGkg'             => $previousRecord->fat_gkg,
            ] : null,
        ]);
    }

    public function finish(Request $request, Appointment $appointment)
    {
        abort_if($appointment->nutritionist_id !== $request->user()->id, 403);

        $request->validate([
            'weight'           => 'nullable|numeric|min:1|max:500',
            'height'           => 'nullable|numeric|min:50|max:250',
            'body_fat'         => 'nullable|numeric|min:1|max:100',
            'muscle_mass'      => 'nullable|numeric|min:1|max:300',
            'waist'            => 'nullable|numeric|min:1|max:300',
            'hip'              => 'nullable|numeric|min:1|max:300',
            'blood_pressure'   => 'nullable|string|max:20',
            'summary'          => 'nullable|string|max:5000',
            // Circunferencias Lee
            'arm_circ'         => 'nullable|numeric|min:1|max:100',
            'arm_contracted'   => 'nullable|numeric|min:1|max:100',
            'abdomen_circ'     => 'nullable|numeric|min:30|max:300',
            'thigh_circ'       => 'nullable|numeric|min:1|max:150',
            'calf_circ'        => 'nullable|numeric|min:1|max:100',
            'tricep_skinfold'  => 'nullable|numeric|min:1|max:80',
            'thigh_skinfold'   => 'nullable|numeric|min:1|max:80',
            'calf_skinfold'    => 'nullable|numeric|min:1|max:80',
            'muscle_mass_method' => 'nullable|in:lee_full,lee_simple,manual,lean_mass_formula',
            // Plan nutricional
            'activity_factor'  => 'nullable|numeric|in:1.2,1.375,1.55,1.725,1.9',
            'prescription_type' => 'nullable|in:maintenance,bulk,cut',
            'prescribed_kcal'  => 'nullable|numeric|min:500|max:15000',
            'protein_gkg'      => 'nullable|numeric|min:0.5|max:5',
            'fat_gkg'          => 'nullable|numeric|min:0.3|max:3',
            'geb_harris'       => 'nullable|numeric',
            'geb_mifflin'      => 'nullable|numeric',
            'geb_owen'         => 'nullable|numeric',
            'geb_katch'        => 'nullable|numeric',
            'geb_average'      => 'nullable|numeric',
            'get_total'        => 'nullable|numeric',
        ]);

        // Calcular IMC usando la altura del request (puede venir corregida por el nutriólogo)
        $bmi = null;
        if ($request->weight && $request->height) {
            $hm  = $request->height / 100;
            $bmi = round($request->weight / ($hm * $hm), 2);
        } elseif ($request->weight) {
            $profile = PatientProfile::where('user_id', $appointment->patient_id)->first();
            if ($profile?->height) {
                $hm  = $profile->height / 100;
                $bmi = round($request->weight / ($hm * $hm), 2);
            }
        }

        $appointment->update([
            'status'          => 'completed',
            'completed_at'    => now(),
            'weight_at_visit' => $request->weight,
            'summary'         => $request->summary,
        ]);

        // Guardar expediente completo de la consulta
        ConsultationRecord::create([
            'appointment_id'      => $appointment->id,
            'patient_id'          => $appointment->patient_id,
            'nutritionist_id'     => $appointment->nutritionist_id,
            'weight'              => $request->weight,
            'body_fat_percentage' => $request->body_fat,
            'muscle_mass'         => $request->muscle_mass,
            'bmi'                 => $bmi,
            'waist_cm'            => $request->waist,
            'hip_cm'              => $request->hip,
            'blood_pressure'      => $request->blood_pressure,
            'recorded_at'         => now(),
            // Circunferencias Lee et al.
            'arm_circ_cm'         => $request->arm_circ,
            'arm_contracted_cm'   => $request->arm_contracted,
            'abdomen_cm'          => $request->abdomen_circ,
            'thigh_circ_cm'       => $request->thigh_circ,
            'calf_circ_cm'        => $request->calf_circ,
            'tricep_skinfold_mm'  => $request->tricep_skinfold,
            'thigh_skinfold_mm'   => $request->thigh_skinfold,
            'calf_skinfold_mm'    => $request->calf_skinfold,
            'muscle_mass_method'  => $request->muscle_mass_method,
            // Plan nutricional
            'geb_harris'          => $request->geb_harris,
            'geb_mifflin'         => $request->geb_mifflin,
            'geb_owen'            => $request->geb_owen,
            'geb_katch'           => $request->geb_katch,
            'geb_average'         => $request->geb_average,
            'activity_factor'     => $request->activity_factor,
            'get_total'           => $request->get_total,
            'prescription_type'   => $request->prescription_type,
            'prescribed_kcal'     => $request->prescribed_kcal,
            'protein_gkg'         => $request->protein_gkg,
            'fat_gkg'             => $request->fat_gkg,
        ]);

        // Actualizar perfil del paciente con los últimos datos
        $profileData = ['last_consultation' => now()];
        if ($request->weight)     $profileData['current_weight'] = $request->weight;

        PatientProfile::where('user_id', $appointment->patient_id)->update($profileData);

        return redirect()->route('calendario');
    }
}
