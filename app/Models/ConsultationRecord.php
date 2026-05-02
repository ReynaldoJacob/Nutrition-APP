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
        'waist_hip_ratio',
        'neck_cm',
        'wrist_cm',
        'chest_cm',
        'arm_cm',
        'thigh_cm',
        // Circunferencias Lee et al.
        'arm_circ_cm',
        'arm_contracted_cm',
        'abdomen_cm',
        'thigh_circ_cm',
        'calf_circ_cm',
        'circumferences_notes',
        // Pliegues cutáneos
        'tricep_skinfold_mm',
        'biceps_skinfold_mm',
        'subescapular_skinfold_mm',
        'suprailiac_skinfold_mm',
        'abdominal_skinfold_mm',
        'pectoral_skinfold_mm',
        'thigh_skinfold_mm',
        'anterior_thigh_skinfold_mm',
        'calf_skinfold_mm',
        'medial_calf_skinfold_mm',
        'skinfold_equation',
        'body_fat_skinfold_percentage',
        'skinfolds_notes',
        // Composición corporal
        'fat_mass_kg',
        'muscle_mass_kg',
        'body_water_percentage',
        'water_percentage',
        'bone_mass',
        'bone_mass_kg',
        'visceral_fat',
        'visceral_fat_level',
        'basal_metabolism_kcal',
        'metabolic_age',
        'body_composition_notes',
        'muscle_mass_method',
        'blood_pressure',
        'heart_rate',
        'glucose',
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
        'waist_hip_ratio'      => 'decimal:3',
        'neck_cm'              => 'decimal:2',
        'wrist_cm'             => 'decimal:2',
        'chest_cm'             => 'decimal:2',
        'arm_cm'               => 'decimal:2',
        'thigh_cm'             => 'decimal:2',
        // Circunferencias Lee et al.
        'arm_circ_cm'          => 'decimal:2',
        'arm_contracted_cm'    => 'decimal:2',
        'abdomen_cm'           => 'decimal:2',
        'thigh_circ_cm'        => 'decimal:2',
        'calf_circ_cm'         => 'decimal:2',
        // Pliegues cutáneos
        'tricep_skinfold_mm'   => 'decimal:2',
        'biceps_skinfold_mm'   => 'decimal:2',
        'subescapular_skinfold_mm' => 'decimal:2',
        'suprailiac_skinfold_mm'   => 'decimal:2',
        'abdominal_skinfold_mm'    => 'decimal:2',
        'pectoral_skinfold_mm'     => 'decimal:2',
        'thigh_skinfold_mm'    => 'decimal:2',
        'anterior_thigh_skinfold_mm' => 'decimal:2',
        'calf_skinfold_mm'     => 'decimal:2',
        'medial_calf_skinfold_mm'   => 'decimal:2',
        'body_fat_skinfold_percentage' => 'decimal:2',
        // Composición corporal
        'fat_mass_kg'          => 'decimal:2',
        'muscle_mass_kg'       => 'decimal:2',
        'body_water_percentage' => 'decimal:2',
        'water_percentage'     => 'decimal:2',
        'bone_mass'            => 'decimal:2',
        'bone_mass_kg'         => 'decimal:2',
        'visceral_fat'         => 'decimal:2',
        'visceral_fat_level'   => 'integer',
        'basal_metabolism_kcal' => 'integer',
        'metabolic_age'        => 'integer',
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
