<?php

namespace App\Http\Controllers;

use App\Models\ConsultationRecord;
use App\Models\PatientProfile;
use App\Models\User;
use App\Services\PasswordGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PatientController extends Controller
{
    private const FREE_PLAN_MAX_PATIENTS = 5;

    private array $goalLabels = [
        'weight_loss'  => 'Pérdida de Peso',
        'weight_gain'  => 'Aumento de Peso',
        'maintenance'  => 'Mantenimiento',
        'health'       => 'Salud General',
    ];

    private array $statusLabels = [
        'active'   => 'Activo',
        'inactive' => 'Inactivo',
        'paused'   => 'En Pausa',
    ];

    public function index(Request $request)
    {
        $nutritionistId = $request->user()->id;

        $patients = PatientProfile::with('user')
            ->where('nutritionist_id', $nutritionistId)
            ->get()
            ->map(fn($p) => [
                'id'        => '#CS-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                'profileId' => $p->id,
                'name'      => $p->user->full_name,
                'email'     => $p->user->email,
                'avatar'    => $p->user->avatar,
                'lastVisit' => $p->last_consultation?->translatedFormat('d M, Y') ?? 'Sin consulta',
                'daysAgo'   => $p->last_consultation?->diffForHumans() ?? '—',
                'goal'      => $this->goalLabels[$p->nutrition_goal] ?? 'Sin objetivo',
                'status'    => $this->statusLabels[$p->status] ?? $p->status,
            ]);

        $request->user()->loadMissing('nutritionistProfile');
        $planKey = $request->user()->nutritionistProfile?->effectivePlanKey() ?? 'free';
        $patientsCount = PatientProfile::where('nutritionist_id', $nutritionistId)->count();
        $maxPatients = $planKey === 'free' ? self::FREE_PLAN_MAX_PATIENTS : null;

        return Inertia::render('Patients', [
            'patients' => $patients,
            'patientLimits' => [
                'planKey' => $planKey,
                'currentPatients' => $patientsCount,
                'maxPatients' => $maxPatients,
                'isLimitReached' => $maxPatients !== null && $patientsCount >= $maxPatients,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->user()->loadMissing('nutritionistProfile');
        $planKey = $request->user()->nutritionistProfile?->effectivePlanKey() ?? 'free';

        if ($planKey === 'free') {
            $patientsCount = PatientProfile::where('nutritionist_id', $request->user()->id)->count();
            if ($patientsCount >= self::FREE_PLAN_MAX_PATIENTS) {
                throw ValidationException::withMessages([
                    'plan_limit' => 'Has alcanzado el limite de 5 pacientes del plan Free. Mejora tu plan para continuar.',
                ]);
            }
        }

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender'     => ['nullable', 'in:male,female,other'],
            'notes'      => ['nullable', 'string', 'max:2000'],
        ]);

        // Separar nombre completo en first_name / last_name
        $parts     = explode(' ', trim($data['name']), 2);
        $firstName = $parts[0];
        $lastName  = $parts[1] ?? '-';

        $temporaryPassword = PasswordGeneratorService::generateTemporaryPassword();
        $hashedPassword = bcrypt($temporaryPassword);

        DB::transaction(function () use ($data, $firstName, $lastName, $request, $hashedPassword, $temporaryPassword) {
            // Crear usuario con contraseña temporal
            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $data['email'],
                'password'   => $hashedPassword,
                'phone'      => $data['phone'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'gender'     => $data['gender'] ?? null,
                'role_key'   => 'patient',
                'is_active'  => true,
                'must_change_password' => true,
                'temporary_password' => $temporaryPassword,
            ]);

            // Crear perfil vinculado al nutriólogo
            PatientProfile::create([
                'user_id'         => $user->id,
                'nutritionist_id' => $request->user()->id,
                'notes'           => $data['notes'] ?? null,
                'status'          => 'active',
            ]);
        });

        return redirect()->route('pacientes')->with(
            'success',
            "Paciente {$firstName} registrado. Comparte esta contraseña temporal: {$temporaryPassword}"
        );
    }

    public function show(Request $request, int $id)
    {
        $profile = PatientProfile::with('user')
            ->where('id', $id)
            ->where('nutritionist_id', $request->user()->id)
            ->firstOrFail();

        $user = $profile->user;

        // Historial de consultas (más reciente primero)
        $records = ConsultationRecord::where('patient_id', $user->id)
            ->with('appointment')
            ->orderByDesc('recorded_at')
            ->get();

        // Última consulta para métricas actuales
        $latest = $records->first();

        // Calcular tendencia: diferencia con la anterior
        $previous = $records->skip(1)->first();
        $weightDiff    = ($latest && $previous && $latest->weight && $previous->weight)
            ? round($latest->weight - $previous->weight, 1) : null;
        $bodyFatDiff   = ($latest && $previous && $latest->body_fat_percentage && $previous->body_fat_percentage)
            ? round($latest->body_fat_percentage - $previous->body_fat_percentage, 1) : null;
        $muscleDiff    = ($latest && $previous && $latest->muscle_mass && $previous->muscle_mass)
            ? round($latest->muscle_mass - $previous->muscle_mass, 1) : null;

        // Historial para gráfica (últimas 6 consultas)
        $chartData = $records->take(6)->reverse()->values()->map(fn($r) => [
            'label'  => $r->recorded_at->format('M'),
            'weight' => $r->weight,
            'fat'    => $r->body_fat_percentage,
            'muscle' => $r->muscle_mass,
        ]);

        // Timeline de consultas
        $timeline = $records->map(fn($r) => [
            'id'        => $r->id,
            'date'      => $r->recorded_at->translatedFormat('d M, Y'),
            'weight'    => $r->weight,
            'fat'       => $r->body_fat_percentage,
            'muscle'    => $r->muscle_mass,
            'bmi'       => $r->bmi,
            'summary'   => $r->appointment?->summary ?? null,
        ]);

        // Edad
        $age = $user->birth_date ? $user->birth_date->diffInYears(now()) : null;

        return Inertia::render('PatientRecord', [
            'patient' => [
                'id'        => '#CS-' . str_pad($profile->id, 4, '0', STR_PAD_LEFT),
                'profileId' => $profile->id,
                'name'      => $user->full_name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'avatar'    => $user->avatar,
                'age'       => $age,
                'gender'    => $user->gender,
                'height'    => $profile->height,
                'birthDate' => $user->birth_date?->toDateString(),
                'goal'      => $this->goalLabels[$profile->nutrition_goal] ?? 'Sin objetivo',
                'status'    => $this->statusLabels[$profile->status] ?? $profile->status,
                'notes'     => $profile->notes,
                'memberSince' => $profile->created_at->translatedFormat('M Y'),
            ],
            'latest' => $latest ? [
                'weight'    => $latest->weight,
                'fat'       => $latest->body_fat_percentage,
                'muscle'    => $latest->muscle_mass,
                'bmi'       => $latest->bmi,
                'waist'     => $latest->waist_cm,
                'hip'       => $latest->hip_cm,
                'date'      => $latest->recorded_at->translatedFormat('d M, Y'),
            ] : null,
            'trends'    => compact('weightDiff', 'bodyFatDiff', 'muscleDiff'),
            'timeline'  => $timeline,
            'chartData' => $chartData,
        ]);
    }
}
