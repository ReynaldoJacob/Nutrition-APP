<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Models\Appointment;
use App\Models\PatientProfile;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación (solo para invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $nutritionistId = auth()->id();

        $total = \App\Models\PatientProfile::where('nutritionist_id', $nutritionistId)->count();

        $todayAppointments = \App\Models\Appointment::with('patient')
            ->where('nutritionist_id', $nutritionistId)
            ->whereDate('scheduled_at', today())
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn($a) => [
                'id'          => $a->id,
                'patientName' => $a->patient->full_name,
                'patientAvatar' => $a->patient->avatar,
                'notes'       => $a->notes,
                'time'        => $a->scheduled_at->format('h:i A'),
                'type'        => \App\Models\Appointment::TYPE_LABELS[$a->type] ?? $a->type,
                'status'      => $a->status,
            ]);

        $patients = PatientProfile::with('user')
            ->where('nutritionist_id', $nutritionistId)
            ->get()
            ->map(fn($p) => [
                'id'     => $p->id,
                'userId' => $p->user_id,
                'name'   => $p->user->full_name,
            ]);

        return \Inertia\Inertia::render('Dashboard', [
            'totalPatients'     => $total,
            'todayAppointments' => $todayAppointments,
            'patients'          => $patients,
        ]);
    })->name('dashboard');
    Route::get('/pacientes', [PatientController::class, 'index'])->name('pacientes');
    Route::get('/pacientes/{id}', fn($id) => \Inertia\Inertia::render('PatientRecord', ['patientId' => $id]))->name('pacientes.show');

    Route::get('/calendario', function () {
        $nutritionistId = auth()->id();

        // Semana activa: puede ser navegada con ?week=YYYY-MM-DD
        $weekStart = request('week')
            ? \Carbon\Carbon::parse(request('week'))->startOfWeek(\Carbon\Carbon::MONDAY)
            : now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(\Carbon\Carbon::FRIDAY);

        // Citas de la semana (lun–vie)
        $appointments = Appointment::with('patient')
            ->where('nutritionist_id', $nutritionistId)
            ->whereBetween('scheduled_at', [$weekStart->startOfDay(), $weekEnd->endOfDay()])
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn($a) => [
                'id'            => $a->id,
                'patientName'   => $a->patient->full_name,
                'patientAvatar' => $a->patient->avatar,
                'notes'         => $a->notes,
                'summary'       => $a->summary,
                'type'          => $a->type,
                'typeLabel'     => Appointment::TYPE_LABELS[$a->type] ?? $a->type,
                'status'        => $a->status,
                'statusLabel'   => Appointment::STATUS_LABELS[$a->status] ?? $a->status,
                'scheduledAt'   => $a->scheduled_at->toIso8601String(),
                'dayOfWeek'     => (int) $a->scheduled_at->format('N'), // 1=Lun … 5=Vie
                'startHour'     => (int) $a->scheduled_at->format('H'),
                'startMin'      => (int) $a->scheduled_at->format('i'),
                'duration'      => $a->duration,
                'timeRange'     => $a->scheduled_at->format('h:i A') . ' - ' . $a->scheduled_at->addMinutes($a->duration)->format('h:i A'),
            ]);

        $patients = PatientProfile::with('user')
            ->where('nutritionist_id', $nutritionistId)
            ->get()
            ->map(fn($p) => [
                'id'     => $p->id,
                'userId' => $p->user_id,
                'name'   => $p->user->full_name,
            ]);

        return \Inertia\Inertia::render('Calendar', [
            'appointments' => $appointments,
            'weekStart'    => $weekStart->toDateString(),
            'weekEnd'      => $weekEnd->toDateString(),
            'weekLabel'    => 'Semana del ' . $weekStart->translatedFormat('d') . ' al ' . $weekEnd->translatedFormat('d \d\e F'),
            'monthLabel'   => ucfirst($weekStart->translatedFormat('F Y')),
            'today'        => now()->toDateString(),
            'patients'     => $patients,
        ]);
    })->name('calendario');

    Route::post('/citas', [AppointmentController::class, 'store'])->name('citas.store');
    Route::patch('/citas/{appointment}/cancelar', [AppointmentController::class, 'cancel'])->name('citas.cancel');
    Route::get('/citas/{appointment}/consulta', [AppointmentController::class, 'start'])->name('citas.start');
    Route::post('/citas/{appointment}/finalizar', [AppointmentController::class, 'finish'])->name('citas.finish');
});
