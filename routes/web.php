<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NutritionPlanController;
use App\Http\Controllers\Api\FoodCatalogController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PricingController;
use App\Models\Appointment;
use App\Models\PatientProfile;
use Illuminate\Support\Facades\Route;

Route::get('/planes', [PricingController::class, 'index'])->name('plans');

// Rutas de autenticación (solo para invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
    Route::get('/registro/verificar', [AuthController::class, 'showVerifyOtp'])->name('register.verify');
    Route::post('/registro/verificar', [AuthController::class, 'verifyOtp'])->name('register.verify.submit');
    Route::post('/registro/verificar/reenviar', [AuthController::class, 'resendOtp'])->name('register.verify.resend');
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

        $adminNotices = \App\Models\AdminNotice::with('admin')
            ->where('recipient_user_id', $nutritionistId)
            ->latest('sent_at')
            ->take(10)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'category' => $n->category,
                'title'    => $n->title,
                'message'  => $n->message,
                'admin'    => $n->admin?->full_name ?? 'Administración',
                'sentAt'   => optional($n->sent_at)->translatedFormat('d M, h:i A'),
            ]);

        return \Inertia\Inertia::render('Dashboard', [
            'totalPatients'     => $total,
            'todayAppointments' => $todayAppointments,
            'patients'          => $patients,
            'adminNotices'      => $adminNotices,
        ]);
    })->name('dashboard');
    Route::get('/pacientes', [PatientController::class, 'index'])->name('pacientes');
    Route::post('/pacientes', [PatientController::class, 'store'])->name('pacientes.store');
    Route::get('/pacientes/{id}', [PatientController::class, 'show'])->name('pacientes.show');

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

    Route::get('/config', [ConfigController::class, 'index'])->name('config');
    Route::patch('/config/theme', [ConfigController::class, 'updateTheme'])->name('config.theme');
    Route::post('/config/logo', [ConfigController::class, 'updateClinicLogo'])->name('config.logo');

    Route::get('/api/foods', [FoodCatalogController::class, 'index'])->name('api.foods.index');
    Route::get('/api/foods/fatsecret/search', [FoodCatalogController::class, 'searchFatSecret'])
        ->middleware('throttle:fatsecret-search')
        ->name('api.foods.fatsecret.search');

    Route::get('/planes-alimenticios', [NutritionPlanController::class, 'index'])->name('nutrition-plans.index');
    Route::get('/planes-alimenticios/{id}', [NutritionPlanController::class, 'show'])->name('nutrition-plans.show');
    Route::post('/planes-alimenticios', [NutritionPlanController::class, 'store'])->name('nutrition-plans.store');
    Route::patch('/planes-alimenticios/{id}', [NutritionPlanController::class, 'update'])->name('nutrition-plans.update');
    Route::delete('/planes-alimenticios/{id}', [NutritionPlanController::class, 'destroy'])->name('nutrition-plans.destroy');

    Route::patch('/notificaciones/vistas', [NotificationController::class, 'markAsSeen'])->name('notifications.seen');

    // Rutas de administrador
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/nutriologos', [AdminController::class, 'nutritionists'])->name('admin.nutriologos');
        Route::post('/nutriologos', [AdminController::class, 'storeNutritionist'])->name('admin.nutriologos.store');
        Route::patch('/nutriologos/{id}/toggle', [AdminController::class, 'toggleNutritionist'])->name('admin.nutriologos.toggle');
        Route::get('/avisos', [AdminController::class, 'notices'])->name('admin.avisos');
        Route::post('/avisos', [AdminController::class, 'sendNotice'])->name('admin.avisos.send');
    });
});
