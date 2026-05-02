<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nutritionist_id')->constrained('users')->cascadeOnDelete();

            // Información básica
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();

            // Clasificación
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack', 'dessert', 'beverage'])->nullable();
            $table->string('diet_type', 100)->nullable()->comment('keto, vegan, gluten_free, paleo');

            // Detalles de preparación
            $table->unsignedInteger('preparation_time')->nullable()->comment('minutos');
            $table->unsignedInteger('servings')->default(1);
            $table->longText('instructions')->nullable();

            // Macronutrientes
            $table->unsignedDecimal('calories', 8, 2)->nullable()->comment('kcal');
            $table->unsignedDecimal('protein', 8, 2)->nullable()->comment('gramos');
            $table->unsignedDecimal('fats', 8, 2)->nullable()->comment('gramos');
            $table->unsignedDecimal('carbs', 8, 2)->nullable()->comment('gramos');

            // Estado
            $table->boolean('is_featured')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
