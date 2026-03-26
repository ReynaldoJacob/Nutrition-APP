<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutritionist_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Datos profesionales
            $table->string('license_number', 50)->unique();
            $table->string('specialization', 100)->nullable();
            $table->text('biography')->nullable();
            $table->unsignedSmallInteger('years_experience')->default(0);

            // Formación académica
            $table->string('university', 150)->nullable();
            $table->string('degree', 100)->nullable();
            $table->year('graduation_year')->nullable();

            // Consulta
            $table->unsignedDecimal('consultation_fee', 8, 2)->default(0);
            $table->unsignedSmallInteger('consultation_duration')->default(60)->comment('minutos');
            $table->json('available_days')->nullable()->comment('["monday","wednesday","friday"]');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Métricas
            $table->boolean('is_verified')->default(false);
            $table->unsignedDecimal('rating', 3, 2)->default(0)->comment('0.00 - 5.00');
            $table->unsignedInteger('total_patients')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutritionist_profiles');
    }
};
