<?php

namespace Database\Seeders;

use App\Models\NutritionistProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NutritionistSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'nutriologa@clinicalsanctuary.com'],
            [
                'first_name' => 'Clara',
                'last_name'  => 'Mercado',
                'password'   => Hash::make('test2026'),
                'phone'      => '5512345678',
                'birth_date' => '1990-06-15',
                'gender'     => 'female',
                'role_key'   => 'nutritionist',
                'is_active'  => 1,
            ]
        );

        NutritionistProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'license_number'        => 'NUT-2024-001',
                'specialization'        => 'Nutrición Clínica y Deportiva',
                'biography'             => 'Nutrióloga certificada con más de 8 años de experiencia en nutrición clínica, deportiva y control de peso. Apasionada por transformar hábitos alimenticios y mejorar la calidad de vida de sus pacientes.',
                'years_experience'      => 8,
                'university'            => 'Universidad Nacional Autónoma de México',
                'degree'                => 'Licenciatura en Nutrición',
                'graduation_year'       => 2016,
                'consultation_fee'      => 800.00,
                'consultation_duration' => 60,
                'available_days'        => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'start_time'            => '09:00:00',
                'end_time'              => '18:00:00',
                'is_verified'           => true,
                'rating'                => 4.90,
                'total_patients'        => 0,
            ]
        );
    }
}
