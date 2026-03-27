<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->nutritionistProfile;

        return Inertia::render('Config', [
            'themeColor'           => $profile?->theme_color ?? 'emerald',
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
}
