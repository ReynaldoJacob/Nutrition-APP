<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $nutritionist = User::where('email', 'nutriologa@clinicalsanctuary.com')->first();
        $patient      = User::where('email', 'paciente@clinicalsanctuary.com')->first();

        if (! $nutritionist || ! $patient) {
            return;
        }

        // Cita completada (la semana pasada)
        Appointment::firstOrCreate(
            [
                'patient_id'      => $patient->id,
                'nutritionist_id' => $nutritionist->id,
                'scheduled_at'    => now()->subDays(7)->setTime(9, 0),
            ],
            [
                'duration'        => 60,
                'type'            => 'initial',
                'status'          => 'completed',
                'notes'           => 'Primera consulta. Paciente refiere antecedentes de Diabetes Tipo II y alergia a lactosa.',
                'summary'         => 'Se realizó evaluación nutricional inicial. IMC: 28.7. Se establece plan de alimentación hipocalórico con restricción de lactosa. Objetivo: reducir 1 kg por semana.',
                'weight_at_visit' => 75.50,
                'confirmed_at'    => now()->subDays(8),
                'completed_at'    => now()->subDays(7)->setTime(10, 0),
            ]
        );

        // Cita de seguimiento (hoy)
        Appointment::firstOrCreate(
            [
                'patient_id'      => $patient->id,
                'nutritionist_id' => $nutritionist->id,
                'scheduled_at'    => now()->setTime(11, 0),
            ],
            [
                'duration'    => 60,
                'type'        => 'follow_up',
                'status'      => 'confirmed',
                'notes'       => 'Revisión de adherencia al plan alimenticio. Traer registro de alimentos de la semana.',
                'confirmed_at' => now()->subDay(),
            ]
        );

        // Cita próxima (en una semana)
        Appointment::firstOrCreate(
            [
                'patient_id'      => $patient->id,
                'nutritionist_id' => $nutritionist->id,
                'scheduled_at'    => now()->addDays(7)->setTime(9, 0),
            ],
            [
                'duration' => 60,
                'type'     => 'follow_up',
                'status'   => 'scheduled',
                'notes'    => 'Control de peso y ajuste de plan si es necesario.',
            ]
        );
    }
}
