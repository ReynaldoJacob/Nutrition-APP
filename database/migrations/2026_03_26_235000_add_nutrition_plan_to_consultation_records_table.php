<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_records', function (Blueprint $table) {
            // GEB - Gasto Energético Basal (múltiples fórmulas)
            $table->unsignedDecimal('geb_harris',  8, 2)->nullable()->comment('kcal - fórmula Harris-Benedict')->after('blood_pressure');
            $table->unsignedDecimal('geb_mifflin', 8, 2)->nullable()->comment('kcal - fórmula Mifflin-St Jeor')->after('geb_harris');
            $table->unsignedDecimal('geb_owen',    8, 2)->nullable()->comment('kcal - fórmula Owen')->after('geb_mifflin');
            $table->unsignedDecimal('geb_katch',   8, 2)->nullable()->comment('kcal - fórmula Katch-McArdle (requiere % grasa)')->after('geb_owen');
            $table->unsignedDecimal('geb_average', 8, 2)->nullable()->comment('kcal - promedio de fórmulas disponibles')->after('geb_katch');

            // GET - Gasto Energético Total
            $table->decimal('activity_factor', 4, 3)->nullable()->comment('1.2=sedentario … 1.9=muy activo')->after('geb_average');
            $table->unsignedDecimal('get_total', 8, 2)->nullable()->comment('kcal - GEB × factor actividad')->after('activity_factor');

            // Prescripción calórica
            $table->string('prescription_type', 20)->nullable()->comment('maintenance | bulk | cut')->after('get_total');
            $table->unsignedDecimal('prescribed_kcal', 8, 2)->nullable()->comment('kcal/día prescritas')->after('prescription_type');

            // Macronutrientes (g/kg de peso corporal)
            $table->decimal('protein_gkg', 5, 2)->nullable()->comment('g de proteína por kg de peso')->after('prescribed_kcal');
            $table->decimal('fat_gkg',     5, 2)->nullable()->comment('g de grasa por kg de peso')->after('protein_gkg');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_records', function (Blueprint $table) {
            $table->dropColumn([
                'geb_harris', 'geb_mifflin', 'geb_owen', 'geb_katch', 'geb_average',
                'activity_factor', 'get_total',
                'prescription_type', 'prescribed_kcal',
                'protein_gkg', 'fat_gkg',
            ]);
        });
    }
};
