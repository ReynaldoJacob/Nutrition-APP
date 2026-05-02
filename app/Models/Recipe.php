<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nutritionist_id',
        'name',
        'description',
        'image_url',
        'meal_type',
        'diet_type',
        'preparation_time',
        'servings',
        'instructions',
        'calories',
        'protein',
        'fats',
        'carbs',
        'is_featured',
    ];

    protected $casts = [
        'preparation_time' => 'integer',
        'servings' => 'integer',
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'fats' => 'decimal:2',
        'carbs' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function nutritionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }
}
