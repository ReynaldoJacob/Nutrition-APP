<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PatientDashboardController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $patientProfile = $user->patientProfile;

        if (!$patientProfile) {
            return redirect('/login');
        }

        // Obtener la nutrióloga
        $nutritionist = $patientProfile->nutritionist;

        if (! $nutritionist) {
            return redirect('/')->with('warning', 'No se encontro una nutriologa asociada a este paciente.');
        }

        // Obtener próxima cita
        $nextAppointment = Appointment::where('patient_id', $patientProfile->id)
            ->where('status', '!=', 'cancelled')
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->first();

        // Obtener contenido publicado por la nutrióloga
        $recipes = ContentItem::where('nutritionist_id', $nutritionist->id)
            ->where('content_type', 'recipe')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->take(1)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->excerpt ?? substr($r->body, 0, 150),
                'image_url' => $r->cover_image_path ? asset('storage/' . $r->cover_image_path) : 'https://via.placeholder.com/400x300',
                'reading_time' => max((int) ($r->reading_time_minutes ?? 5), 1),
            ])
            ->toArray();

        $medicalAdvice = ContentItem::where('nutritionist_id', $nutritionist->id)
            ->where('content_type', 'article')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->take(1)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'title' => $m->title,
                'content' => $m->excerpt ?? substr($m->body, 0, 200),
            ])
            ->toArray();

        $dailyHabits = ContentItem::where('nutritionist_id', $nutritionist->id)
            ->where('content_type', 'news')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->take(1)
            ->get()
            ->map(fn($h) => [
                'id' => $h->id,
                'title' => $h->title,
                'description' => $h->excerpt ?? substr($h->body, 0, 200),
            ])
            ->toArray();

        $totalPublishedContent = ContentItem::query()
            ->where('nutritionist_id', $nutritionist->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->count();

        // Obtener medidas del paciente (últimas)
        $measurements = [
            'current_weight' => $patientProfile->current_weight ?? $patientProfile->weight ?? 70,
            'target_weight' => $patientProfile->target_weight ?? 65,
            'initial_weight' => $patientProfile->weight ?? 75,
            'body_fat' => $patientProfile->body_fat_percentage ?? 22,
            'body_fat_change' => '-0.5',
            'muscle_mass' => $patientProfile->muscle_mass ?? 31,
            'muscle_change' => '+0.2',
            'weight_change' => '-1.2',
        ];

        return Inertia::render('PatientDashboard', [
            'patient' => [
                'user' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ],
                'measurements' => $measurements,
            ],
            'nutritionist' => [
                'id' => $nutritionist->id,
                'first_name' => $nutritionist->first_name,
                'last_name' => $nutritionist->last_name,
                'full_name' => $nutritionist->full_name,
                'avatar' => $nutritionist->avatar,
            ],
            'recipes' => $recipes,
            'medicalAdvice' => $medicalAdvice,
            'dailyHabits' => $dailyHabits,
            'nextAppointment' => $nextAppointment ? [
                'id' => $nextAppointment->id,
                'scheduled_at' => $nextAppointment->scheduled_at->toIso8601String(),
                'type' => $nextAppointment->type,
                'status' => $nextAppointment->status,
            ] : null,
            'contentUpdates' => $totalPublishedContent,
            'activePatients' => 1200,
        ]);
    }

    public function plan(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $patientProfile = $user->patientProfile;

        if (!$patientProfile) {
            return redirect('/');
        }

        // TODO: Cargar plan de nutrición
        return Inertia::render('PatientPlan');
    }

    public function progress(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $patientProfile = $user->patientProfile;

        if (!$patientProfile) {
            return redirect('/');
        }

        // TODO: Cargar gráficos de progreso
        return Inertia::render('PatientProgress');
    }

    public function appointments(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $patientProfile = $user->patientProfile;

        if (!$patientProfile) {
            return redirect('/');
        }

        // TODO: Cargar citas
        return Inertia::render('PatientAppointments');
    }

    public function community(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $patientProfile = $user->patientProfile;

        if (!$patientProfile) {
            return redirect('/');
        }

        // TODO: Cargar comunidad
        return Inertia::render('PatientCommunity');
    }
}
