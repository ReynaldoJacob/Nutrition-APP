<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nutritionist_id',
        'name',
        'description',
        'category',
        'unit_of_measure',
        'calories_per_unit',
        'protein_per_unit',
        'fats_per_unit',
        'carbs_per_unit',
    ];

    protected $casts = [
        'calories_per_unit' => 'decimal:2',
        'protein_per_unit' => 'decimal:2',
        'fats_per_unit' => 'decimal:2',
        'carbs_per_unit' => 'decimal:2',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }
}
