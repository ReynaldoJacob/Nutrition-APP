<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PatientController extends Controller
{
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
        $patients = PatientProfile::with('user')
            ->where('nutritionist_id', $request->user()->id)
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

        return Inertia::render('Patients', [
            'patients' => $patients,
        ]);
    }

    public function store(Request $request)
    {
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

        DB::transaction(function () use ($data, $firstName, $lastName, $request) {
            // Crear usuario sin password (paciente no inicia sesión)
            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $data['email'],
                'password'   => bcrypt(Str::random(32)),
                'phone'      => $data['phone'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'gender'     => $data['gender'] ?? null,
                'role_key'   => 'patient',
                'is_active'  => true,
            ]);

            // Crear perfil vinculado al nutriólogo
            PatientProfile::create([
                'user_id'         => $user->id,
                'nutritionist_id' => $request->user()->id,
                'notes'           => $data['notes'] ?? null,
                'status'          => 'active',
            ]);
        });

        return redirect()->route('pacientes')->with('success', 'Paciente registrado correctamente.');
    }
}
