<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ConfigController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->nutritionistProfile;

        return Inertia::render('Config', [
            'themeColor'           => $profile?->theme_color ?? 'emerald',
            'clinicLogoUrl'        => $profile?->clinic_logo_path ? asset('storage/' . $profile->clinic_logo_path) : null,
            'consultationDuration' => $profile?->consultation_duration ?? 45,
            'specialization'       => $profile?->specialization ?? '',
            'licenseNumber'        => $profile?->license_number ?? '',
        ]);
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme_color' => ['required', 'string', 'in:emerald,blue,purple,rose,amber'],
        ]);

        $profile = auth()->user()->nutritionistProfile;

        if ($profile) {
            $profile->update(['theme_color' => $request->theme_color]);
        }

        return back();
    }

    public function updateClinicLogo(Request $request)
    {
        $request->validate([
            'clinic_logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = auth()->user()->nutritionistProfile;

        if (! $profile) {
            return back()->withErrors([
                'clinic_logo' => 'No se encontró el perfil del nutriólogo.',
            ]);
        }

        if ($profile->clinic_logo_path) {
            Storage::disk('public')->delete($profile->clinic_logo_path);
        }

        $path = $request->file('clinic_logo')->store('clinic-logos', 'public');

        $profile->update([
            'clinic_logo_path' => $path,
        ]);

        return back();
    }
}
