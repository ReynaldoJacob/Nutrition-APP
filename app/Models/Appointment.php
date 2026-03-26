<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'nutritionist_id',
        'scheduled_at',
        'duration',
        'type',
        'status',
        'notes',
        'summary',
        'weight_at_visit',
        'cancelled_reason',
        'cancelled_by_id',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at'   => 'datetime',
        'confirmed_at'   => 'datetime',
        'completed_at'   => 'datetime',
        'weight_at_visit' => 'decimal:2',
        'duration'       => 'integer',
    ];

    // Etiquetas legibles
    public const TYPE_LABELS = [
        'initial'    => 'Primera Consulta',
        'follow_up'  => 'Seguimiento',
        'emergency'  => 'Urgencia',
        'online'     => 'En Línea',
    ];

    public const STATUS_LABELS = [
        'scheduled'  => 'Programada',
        'confirmed'  => 'Confirmada',
        'completed'  => 'Completada',
        'cancelled'  => 'Cancelada',
        'no_show'    => 'No se presentó',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function nutritionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_id');
    }
}
