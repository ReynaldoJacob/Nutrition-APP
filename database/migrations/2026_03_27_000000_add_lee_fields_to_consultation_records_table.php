<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_records', function (Blueprint $table) {
            // Circunferencias para fórmula Lee et al. (2000) de masa muscular
            $table->unsignedDecimal('arm_circ_cm',   5, 2)->nullable()->comment('Perímetro brazo relajado cm')->after('hip_cm');
            $table->unsignedDecimal('thigh_circ_cm', 5, 2)->nullable()->comment('Perímetro muslo cm')->after('arm_circ_cm');
            $table->unsignedDecimal('calf_circ_cm',  5, 2)->nullable()->comment('Perímetro pantorrilla cm')->after('thigh_circ_cm');
            // Pliegues para corrección (opcionales, mejoran precisión)
            $table->unsignedDecimal('tricep_skinfold_mm',  5, 2)->nullable()->comment('Pliegue tríceps mm')->after('calf_circ_cm');
            $table->unsignedDecimal('thigh_skinfold_mm',   5, 2)->nullable()->comment('Pliegue muslo mm')->after('tricep_skinfold_mm');
            $table->unsignedDecimal('calf_skinfold_mm',    5, 2)->nullable()->comment('Pliegue pantorrilla mm')->after('thigh_skinfold_mm');
            // Método utilizado para calcular masa muscular
            $table->string('muscle_mass_method', 30)->nullable()->comment('lee_full|lee_simple|manual')->after('calf_skinfold_mm');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_records', function (Blueprint $table) {
            $table->dropColumn([
                'arm_circ_cm', 'thigh_circ_cm', 'calf_circ_cm',
                'tricep_skinfold_mm', 'thigh_skinfold_mm', 'calf_skinfold_mm',
                'muscle_mass_method',
            ]);
        });
    }
};
