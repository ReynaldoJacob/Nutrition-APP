<?php

namespace Database\Seeders;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener la nutrióloga ya existente
        $nutritionist = User::where('email', 'nutriologa@clinicalsanctuary.com')->first();

        // Crear usuario paciente
        $patient = User::firstOrCreate(
            ['email' => 'paciente@clinicalsanctuary.com'],
            [
                'first_name' => 'Lucía',
                'last_name'  => 'Martínez',
                'password'   => Hash::make('test2026'),
                'phone'      => '5598765432',
                'birth_date' => '1992-03-22',
                'gender'     => 'female',
                'role_key'   => 'patient',
                'is_active'  => 1,
            ]
        );

        // Crear perfil de paciente vinculado a la nutrióloga
        PatientProfile::firstOrCreate(
            ['user_id' => $patient->id],
            [
                'height'                         => 162.00,
                'current_weight'                 => 75.50,
                'goal_weight'                    => 62.00,
                'initial_weight'                 => 75.50,
                'activity_level'                 => 'light',
                'nutrition_goal'                 => 'weight_loss',
                'medical_conditions'             => ['Diabetes Tipo II'],
                'allergies'                      => ['Lactosa', 'Mariscos'],
                'medications'                    => ['Metformina 500mg'],
                'dietary_restrictions'           => ['Sin gluten'],
                'blood_type'                     => 'O+',
                'emergency_contact_name'         => 'Roberto Martínez',
                'emergency_contact_phone'        => '5511223344',
                'emergency_contact_relationship' => 'Hermano',
                'status'                         => 'active',
                'nutritionist_id'                => $nutritionist?->id,
                'last_consultation'              => now()->subDays(7),
            ]
        );
    }
}
