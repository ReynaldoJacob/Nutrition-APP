<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationRecord extends Model
{
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'nutritionist_id',
        'weight',
        'body_fat_percentage',
        'muscle_mass',
        'bmi',
        'waist_cm',
        'hip_cm',
        'neck_cm',
        'chest_cm',
        'arm_cm',
        'thigh_cm',
        // Circunferencias Lee et al.
        'arm_circ_cm',
        'arm_contracted_cm',
        'abdomen_cm',
        'thigh_circ_cm',
        'calf_circ_cm',
        'tricep_skinfold_mm',
        'thigh_skinfold_mm',
        'calf_skinfold_mm',
        'muscle_mass_method',
        'blood_pressure',
        'heart_rate',
        'glucose',
        'visceral_fat',
        'bone_mass',
        'body_water_percentage',
        'recorded_at',
        // Plan nutricional
        'geb_harris',
        'geb_mifflin',
        'geb_owen',
        'geb_katch',
        'geb_average',
        'activity_factor',
        'get_total',
        'prescription_type',
        'prescribed_kcal',
        'protein_gkg',
        'fat_gkg',
    ];

    protected $casts = [
        'weight'               => 'decimal:2',
        'body_fat_percentage'  => 'decimal:2',
        'muscle_mass'          => 'decimal:2',
        'bmi'                  => 'decimal:2',
        'waist_cm'             => 'decimal:2',
        'hip_cm'               => 'decimal:2',
        'neck_cm'              => 'decimal:2',
        'chest_cm'             => 'decimal:2',
        'arm_cm'               => 'decimal:2',
        'thigh_cm'             => 'decimal:2',
        // Circunferencias Lee et al.
        'arm_circ_cm'          => 'decimal:2',
        'arm_contracted_cm'    => 'decimal:2',
        'abdomen_cm'           => 'decimal:2',
        'thigh_circ_cm'        => 'decimal:2',
        'calf_circ_cm'         => 'decimal:2',
        'tricep_skinfold_mm'   => 'decimal:2',
        'thigh_skinfold_mm'    => 'decimal:2',
        'calf_skinfold_mm'     => 'decimal:2',
        'visceral_fat'         => 'decimal:2',
        'bone_mass'            => 'decimal:2',
        'body_water_percentage' => 'decimal:2',
        'recorded_at'          => 'datetime',
        // Plan nutricional
        'geb_harris'    => 'decimal:2',
        'geb_mifflin'   => 'decimal:2',
        'geb_owen'      => 'decimal:2',
        'geb_katch'     => 'decimal:2',
        'geb_average'   => 'decimal:2',
        'activity_factor' => 'decimal:3',
        'get_total'     => 'decimal:2',
        'prescribed_kcal' => 'decimal:2',
        'protein_gkg'   => 'decimal:2',
        'fat_gkg'       => 'decimal:2',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function nutritionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id');
    }
}
