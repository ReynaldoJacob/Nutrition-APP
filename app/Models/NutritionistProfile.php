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
        'subscription_plan_key',
        'subscribed_at',
        'subscription_expires_at',
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
        'subscribed_at'       => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    /**
     * Retorna el plan realmente activo del usuario.
     * - Plan 'free' nunca vence → siempre retorna 'free'.
     * - Planes de pago (normal/pro): retorna el plan si subscription_expires_at
     *   es una fecha futura; de lo contrario retorna 'free' (venció o nunca pagó).
     */
    public function effectivePlanKey(): string
    {
        if ($this->subscription_plan_key === 'free') {
            return 'free';
        }

        if ($this->subscription_expires_at && $this->subscription_expires_at->isFuture()) {
            return $this->subscription_plan_key;
        }

        return 'free'; // Vencido o sin fecha de vencimiento asignada
    }

    /**
     * Indica si la suscripción de pago está activa en este momento.
     */
    public function isSubscriptionActive(): bool
    {
        return $this->effectivePlanKey() !== 'free'
            || $this->subscription_plan_key === 'free';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
