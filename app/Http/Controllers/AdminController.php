<?php

namespace App\Http\Controllers;

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
}
