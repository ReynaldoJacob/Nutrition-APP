<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'birth_date',
        'gender',
        'role_key',
        'is_active',
        'last_login_at',
        'avatar',
        'must_change_password',
        'temporary_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date'        => 'date',
        'last_login_at'     => 'datetime',
        'notifications_last_seen_at' => 'datetime',
        'is_active'         => 'boolean',
        'must_change_password' => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function nutritionistProfile(): HasOne
    {
        return $this->hasOne(NutritionistProfile::class);
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class);
    }

    public function contentItems(): HasMany
    {
        return $this->hasMany(ContentItem::class, 'nutritionist_id');
    }

    // Citas como paciente
    public function appointmentsAsPatient(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    // Citas como nutriólogo
    public function appointmentsAsNutritionist(): HasMany
    {
        return $this->hasMany(Appointment::class, 'nutritionist_id');
    }

    public function emailVerificationOtps(): HasMany
    {
        return $this->hasMany(EmailVerificationOtp::class);
    }
}
