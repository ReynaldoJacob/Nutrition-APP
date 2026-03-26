<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'height',
        'current_weight',
        'goal_weight',
        'initial_weight',
        'activity_level',
        'nutrition_goal',
        'notes',
        'medical_conditions',
        'allergies',
        'medications',
        'dietary_restrictions',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'status',
        'nutritionist_id',
        'last_consultation',
    ];

    protected $casts = [
        'medical_conditions'  => 'array',
        'allergies'           => 'array',
        'medications'         => 'array',
        'dietary_restrictions' => 'array',
        'height'              => 'decimal:2',
        'current_weight'      => 'decimal:2',
        'goal_weight'         => 'decimal:2',
        'initial_weight'      => 'decimal:2',
        'last_consultation'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nutritionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id');
    }
}
