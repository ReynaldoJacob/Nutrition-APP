<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use Illuminate\Http\Request;
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
}
