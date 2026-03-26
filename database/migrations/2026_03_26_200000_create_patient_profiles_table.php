<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Métricas físicas
            $table->unsignedDecimal('height', 5, 2)->nullable()->comment('cm');
            $table->unsignedDecimal('current_weight', 5, 2)->nullable()->comment('kg');
            $table->unsignedDecimal('goal_weight', 5, 2)->nullable()->comment('kg');
            $table->unsignedDecimal('initial_weight', 5, 2)->nullable()->comment('kg');

            // Estilo de vida
            $table->enum('activity_level', ['sedentary', 'light', 'moderate', 'active', 'very_active'])->default('sedentary');
            $table->string('nutrition_goal', 100)->nullable()->comment('weight_loss, weight_gain, maintenance, health');

            // Información médica
            $table->json('medical_conditions')->nullable();
            $table->json('allergies')->nullable();
            $table->json('medications')->nullable();
            $table->json('dietary_restrictions')->nullable();
            $table->string('blood_type', 5)->nullable()->comment('A+, A-, B+, B-, AB+, AB-, O+, O-');

            // Contacto de emergencia
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relationship', 60)->nullable();

            // Estado y asignación
            $table->enum('status', ['active', 'inactive', 'paused'])->default('active');
            $table->foreignId('nutritionist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_consultation')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};
