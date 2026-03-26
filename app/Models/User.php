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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date'        => 'date',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
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
}
