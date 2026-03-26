<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Participantes
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nutritionist_id')->constrained('users')->cascadeOnDelete();

            // Programación
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration')->default(60)->comment('minutos');

            // Tipo y estado
            $table->enum('type', ['initial', 'follow_up', 'emergency', 'online'])->default('follow_up');
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('scheduled');

            // Notas clínicas
            $table->text('notes')->nullable()->comment('Notas previas a la cita');
            $table->text('summary')->nullable()->comment('Resumen clínico post-consulta');
            $table->unsignedDecimal('weight_at_visit', 5, 2)->nullable()->comment('Peso registrado en la visita (kg)');

            // Cancelación
            $table->string('cancelled_reason', 255)->nullable();
            $table->foreignId('cancelled_by_id')->nullable()->constrained('users')->nullOnDelete();

            // Confirmación y cierre
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
