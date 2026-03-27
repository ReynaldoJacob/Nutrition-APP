<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NutritionistProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'license_number',
        'specialization',
        'biography',
        'years_experience',
        'university',
        'degree',
        'graduation_year',
        'consultation_fee',
        'consultation_duration',
        'available_days',
        'start_time',
        'end_time',
        'theme_color',
        'clinic_logo_path',
        'is_verified',
        'rating',
        'total_patients',
    ];

    protected $casts = [
        'available_days'      => 'array',
        'is_verified'         => 'boolean',
        'consultation_fee'    => 'decimal:2',
        'rating'              => 'decimal:2',
        'graduation_year'     => 'integer',
        'years_experience'    => 'integer',
        'total_patients'      => 'integer',
        'consultation_duration' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
