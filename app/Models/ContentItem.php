<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentItem extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_LABELS = [
        'recipe' => 'Receta',
        'article' => 'Artículo',
        'news' => 'Noticia',
    ];

    public const STATUS_LABELS = [
        'draft' => 'Borrador',
        'published' => 'Publicado',
        'scheduled' => 'Programado',
    ];

    protected $fillable = [
        'nutritionist_id',
        'content_type',
        'title',
        'excerpt',
        'body',
        'reading_time_minutes',
        'status',
        'cover_image_path',
        'published_at',
    ];

    protected $casts = [
        'reading_time_minutes' => 'integer',
        'published_at' => 'datetime',
    ];

    public function nutritionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id');
    }

    public function scopeVisibleToPatient($query, User $patient)
    {
        $nutritionistId = $patient->patientProfile?->nutritionist_id;

        return $query
            ->where('nutritionist_id', $nutritionistId)
            ->where('status', 'published')
            ->whereNotNull('published_at');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->content_type] ?? ucfirst($this->content_type);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path ? asset('storage/' . $this->cover_image_path) : null;
    }
}
