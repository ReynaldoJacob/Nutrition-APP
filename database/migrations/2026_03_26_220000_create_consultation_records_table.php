<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_records', function (Blueprint $table) {
            $table->id();

            // Vínculos
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nutritionist_id')->constrained('users')->cascadeOnDelete();

            // Antropometría principal
            $table->unsignedDecimal('weight', 5, 2)->nullable()->comment('kg');
            $table->unsignedDecimal('body_fat_percentage', 5, 2)->nullable()->comment('%');
            $table->unsignedDecimal('muscle_mass', 5, 2)->nullable()->comment('kg');
            $table->unsignedDecimal('bmi', 5, 2)->nullable()->comment('Índice de Masa Corporal');

            // Circunferencias
            $table->unsignedDecimal('waist_cm', 5, 2)->nullable()->comment('cm');
            $table->unsignedDecimal('hip_cm', 5, 2)->nullable()->comment('cm');
            $table->unsignedDecimal('neck_cm', 5, 2)->nullable()->comment('cm');
            $table->unsignedDecimal('chest_cm', 5, 2)->nullable()->comment('cm');
            $table->unsignedDecimal('arm_cm', 5, 2)->nullable()->comment('cm - bíceps relajado');
            $table->unsignedDecimal('thigh_cm', 5, 2)->nullable()->comment('cm');

            // Signos vitales
            $table->string('blood_pressure', 20)->nullable()->comment('Ej: 120/80');
            $table->unsignedSmallInteger('heart_rate')->nullable()->comment('bpm');
            $table->unsignedDecimal('glucose', 5, 1)->nullable()->comment('mg/dL');

            // Composición corporal avanzada
            $table->unsignedDecimal('visceral_fat', 5, 2)->nullable()->comment('nivel');
            $table->unsignedDecimal('bone_mass', 5, 2)->nullable()->comment('kg');
            $table->unsignedDecimal('body_water_percentage', 5, 2)->nullable()->comment('%');

            // Timestamp de la medición
            $table->timestamp('recorded_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_records');
    }
};
