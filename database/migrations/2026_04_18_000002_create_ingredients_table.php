<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nutritionist_id')->constrained('users')->cascadeOnDelete();

            // Información básica
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable()->comment('protein, carbs, fats, vegetables, fruits');

            // Valores nutricionales
            $table->string('unit_of_measure', 50)->default('g')->comment('g, ml, unit, cup');
            $table->unsignedDecimal('calories_per_unit', 10, 4)->nullable();
            $table->unsignedDecimal('protein_per_unit', 10, 4)->nullable();
            $table->unsignedDecimal('fats_per_unit', 10, 4)->nullable();
            $table->unsignedDecimal('carbs_per_unit', 10, 4)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
