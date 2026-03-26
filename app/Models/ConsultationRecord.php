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
        'blood_pressure',
        'heart_rate',
        'glucose',
        'visceral_fat',
        'bone_mass',
        'body_water_percentage',
        'recorded_at',
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
        'visceral_fat'         => 'decimal:2',
        'bone_mass'            => 'decimal:2',
        'body_water_percentage' => 'decimal:2',
        'recorded_at'          => 'datetime',
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
