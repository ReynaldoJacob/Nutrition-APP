<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'source',
        'external_id',
        'name',
        'name_es',
        'brand',
        'serving_size',
        'serving_unit',
        'calories',
        'protein',
        'carbs',
        'fat',
        'raw_payload',
    ];

    protected $casts = [
        'serving_size' => 'float',
        'calories' => 'float',
        'protein' => 'float',
        'carbs' => 'float',
        'fat' => 'float',
        'raw_payload' => 'array',
    ];
}
