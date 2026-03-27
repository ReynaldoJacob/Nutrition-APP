<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\AdminNotice;
use App\Models\NutritionistProfile;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalNutritionists = User::where('role_key', 'nutritionist')->whereNull('deleted_at')->count();
        $activeNutritionists = User::where('role_key', 'nutritionist')->where('is_active', true)->whereNull('deleted_at')->count();
        $totalPatients = PatientProfile::whereNull('deleted_at')->count();
        $verifiedNutritionists = NutritionistProfile::where('is_verified', true)->count();

        $recentNutritionists = User::with('nutritionistProfile')
            ->where('role_key', 'nutritionist')
            ->whereNull('deleted_at')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'id'             => $u->id,
                'name'           => $u->full_name,
                'email'          => $u->email,
                'avatar'         => $u->avatar,
                'specialization' => $u->nutritionistProfile?->specialization ?? 'Sin especialidad',
                'is_verified'    => $u->nutritionistProfile?->is_verified ?? false,
                'is_active'      => $u->is_active,
                'memberSince'    => $u->created_at->translatedFormat('d M, Y'),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalNutritionists'  => $totalNutritionists,
                'activeNutritionists' => $activeNutritionists,
                'totalPatients'       => $totalPatients,
                'verifiedNutritionists' => $verifiedNutritionists,
            ],
            'recentNutritionists' => $recentNutritionists,
        ]);
    }

    public function nutritionists()
    {
        $nutritionists = User::with('nutritionistProfile')
            ->where('role_key', 'nutritionist')
            ->whereNull('deleted_at')
            ->latest()
            ->get()
            ->map(fn($u) => [
                'id'             => $u->id,
                'name'           => $u->full_name,
                'email'          => $u->email,
                'phone'          => $u->phone,
                'avatar'         => $u->avatar,
                'license'        => $u->nutritionistProfile?->license_number ?? '—',
                'specialization' => $u->nutritionistProfile?->specialization ?? 'Sin especialidad',
                'totalPatients'  => $u->nutritionistProfile?->total_patients ?? 0,
                'is_verified'    => $u->nutritionistProfile?->is_verified ?? false,
                'is_active'      => $u->is_active,
                'memberSince'    => $u->created_at->translatedFormat('d M, Y'),
            ]);

        return Inertia::render('Admin/Nutritionists', [
            'nutritionists' => $nutritionists,
        ]);
    }

    public function storeNutritionist(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'license_number' => ['required', 'string', 'max:50', 'unique:nutritionist_profiles,license_number'],
            'specialization' => ['nullable', 'string', 'max:100'],
        ]);

        $parts     = explode(' ', trim($data['name']), 2);
        $firstName = $parts[0];
        $lastName  = $parts[1] ?? '-';
        $tempPassword = Str::random(12);

        DB::transaction(function () use ($data, $firstName, $lastName, $tempPassword) {
            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $data['email'],
                'password'   => bcrypt($tempPassword),
                'phone'      => $data['phone'] ?? null,
                'role_key'   => 'nutritionist',
                'is_active'  => true,
            ]);

            NutritionistProfile::create([
                'user_id'        => $user->id,
                'license_number' => $data['license_number'],
                'specialization' => $data['specialization'] ?? null,
            ]);
        });

        return redirect()->route('admin.nutriologos')
            ->with('success', "Nutriólogo registrado. Contraseña temporal: {$tempPassword}");
    }

    public function toggleNutritionist(int $id)
    {
        $user = User::where('role_key', 'nutritionist')->findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Nutriólogo activado.' : 'Nutriólogo desactivado.');
    }

    public function notices()
    {
        $nutritionists = User::where('role_key', 'nutritionist')
            ->whereNull('deleted_at')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn($u) => [
                'id'   => $u->id,
                'name' => $u->full_name,
            ]);

        $recentNotices = AdminNotice::with(['admin', 'recipient'])
            ->latest('sent_at')
            ->take(30)
            ->get()
            ->map(fn($n) => [
                'id'            => $n->id,
                'scope'         => $n->scope,
                'category'      => $n->category,
                'title'         => $n->title,
                'message'       => $n->message,
                'sentAt'        => optional($n->sent_at)->translatedFormat('d M Y, h:i A'),
                'adminName'     => $n->admin?->full_name ?? 'Admin',
                'recipientName' => $n->scope === 'all' ? 'Todos los nutriólogos' : ($n->recipient?->full_name ?? 'Usuario'),
            ]);

        return Inertia::render('Admin/Notices', [
            'nutritionists' => $nutritionists,
            'recentNotices' => $recentNotices,
        ]);
    }

    public function sendNotice(Request $request)
    {
        $data = $request->validate([
            'scope'           => ['required', 'in:all,individual'],
            'nutritionist_id' => ['nullable', 'integer', 'exists:users,id'],
            'category'        => ['required', 'in:update,maintenance,policy,training,alert,reminder'],
            'title'           => ['required', 'string', 'max:120'],
            'message'         => ['required', 'string', 'max:2000'],
        ]);

        if ($data['scope'] === 'individual' && empty($data['nutritionist_id'])) {
            return back()->withErrors(['nutritionist_id' => 'Debes seleccionar un nutriólogo para envío individual.']);
        }

        $admin = $request->user();

        $recipientIds = $data['scope'] === 'all'
            ? User::where('role_key', 'nutritionist')->whereNull('deleted_at')->pluck('id')
            : collect([(int) $data['nutritionist_id']]);

        if ($recipientIds->isEmpty()) {
            return back()->withErrors(['scope' => 'No hay nutriólogos disponibles para recibir el aviso.']);
        }

        DB::transaction(function () use ($data, $recipientIds, $admin) {
            $now = now();
            $rows = $recipientIds->map(fn($recipientId) => [
                'admin_id'          => $admin->id,
                'recipient_user_id' => $recipientId,
                'scope'             => $data['scope'],
                'category'          => $data['category'],
                'title'             => $data['title'],
                'message'           => $data['message'],
                'sent_at'           => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ])->all();

            AdminNotice::insert($rows);
        });

        try {
            event(new NotificationSent([
                'type'           => 'admin_notice',
                'scope'          => $data['scope'],
                'target_user_id' => $data['scope'] === 'individual' ? (int) $data['nutritionist_id'] : null,
                'category'       => $data['category'],
                'title'          => $data['title'],
                'message'        => $data['message'],
                'admin_name'     => $admin->full_name,
                'sent_at'        => now()->toIso8601String(),
            ]));
        } catch (\Throwable $e) {
            report($e);
            // Si Pusher no está configurado, el aviso queda guardado igualmente.
        }

        return back()->with('success', $data['scope'] === 'all'
            ? 'Aviso enviado a todos los nutriólogos.'
            : 'Aviso enviado al nutriólogo seleccionado.');
    }
}
