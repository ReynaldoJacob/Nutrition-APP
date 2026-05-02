<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consultation_records', function (Blueprint $table) {
            // ─── 1. CIRCUNFERENCIAS CORPORALES ───────────────────────────────────
            // NOTA: neck_cm ya existe desde la migración inicial
            $table->unsignedDecimal('wrist_cm', 5, 2)->nullable()->comment('Perímetro muñeca cm')->after('calf_circ_cm');
            $table->unsignedDecimal('waist_hip_ratio', 5, 3)->nullable()->comment('Índice cintura-cadera (calculado)')->after('hip_cm');
            $table->text('circumferences_notes')->nullable()->comment('Notas de circunferencias corporales')->after('wrist_cm');

            // ─── 2. PLIEGUES CUTÁNEOS ───────────────────────────────────────────
            $table->unsignedDecimal('biceps_skinfold_mm', 5, 2)->nullable()->comment('Pliegue bicipital mm')->after('tricep_skinfold_mm');
            $table->unsignedDecimal('subescapular_skinfold_mm', 5, 2)->nullable()->comment('Pliegue subescapular mm')->after('biceps_skinfold_mm');
            $table->unsignedDecimal('suprailiac_skinfold_mm', 5, 2)->nullable()->comment('Pliegue suprailiaco mm')->after('subescapular_skinfold_mm');
            $table->unsignedDecimal('abdominal_skinfold_mm', 5, 2)->nullable()->comment('Pliegue abdominal mm')->after('suprailiac_skinfold_mm');
            $table->unsignedDecimal('anterior_thigh_skinfold_mm', 5, 2)->nullable()->comment('Pliegue muslo anterior mm')->after('thigh_skinfold_mm');
            $table->unsignedDecimal('medial_calf_skinfold_mm', 5, 2)->nullable()->comment('Pliegue pantorrilla medial mm')->after('calf_skinfold_mm');
            $table->unsignedDecimal('pectoral_skinfold_mm', 5, 2)->nullable()->comment('Pliegue pectoral mm')->after('biceps_skinfold_mm');

            // Ecuación seleccionada y cálculo de grasa
            $table->enum('skinfold_equation', ['durnin_womersley', 'jackson_pollock_3', 'jackson_pollock_7'])->nullable()->comment('Ecuación utilizada para cálculo de grasa')->after('muscle_mass_method');
            $table->unsignedDecimal('body_fat_skinfold_percentage', 5, 2)->nullable()->comment('% grasa corporal por plicometría')->after('body_fat_percentage');
            $table->text('skinfolds_notes')->nullable()->comment('Notas de pliegues cutáneos')->after('pectoral_skinfold_mm');

            // ─── 3. COMPOSICIÓN CORPORAL (BIOIMPEDANCIA/DEXA) ──────────────────
            $table->unsignedDecimal('fat_mass_kg', 6, 2)->nullable()->comment('Masa grasa kg')->after('body_fat_percentage');
            $table->unsignedDecimal('muscle_mass_kg', 6, 2)->nullable()->comment('Masa muscular kg')->after('muscle_mass');
            $table->unsignedDecimal('water_percentage', 5, 2)->nullable()->comment('Agua corporal %')->after('body_water_percentage');
            $table->unsignedDecimal('bone_mass_kg', 6, 2)->nullable()->comment('Masa ósea kg')->after('bone_mass');
            $table->unsignedSmallInteger('visceral_fat_level', false)->nullable()->comment('Nivel grasa visceral (numérico)')->after('visceral_fat');
            $table->unsignedSmallInteger('basal_metabolism_kcal', false)->nullable()->comment('Metabolismo basal kcal')->after('geb_average');
            $table->unsignedSmallInteger('metabolic_age', false)->nullable()->comment('Edad metabólica (años)')->after('basal_metabolism_kcal');
            $table->text('body_composition_notes')->nullable()->comment('Notas de composición corporal')->after('metabolic_age');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consultation_records', function (Blueprint $table) {
            // ─── 1. CIRCUNFERENCIAS
            $table->dropColumn([
                'wrist_cm',
                'waist_hip_ratio',
                'circumferences_notes',
            ]);

            // ─── 2. PLIEGUES
            $table->dropColumn([
                'biceps_skinfold_mm',
                'subescapular_skinfold_mm',
                'suprailiac_skinfold_mm',
                'abdominal_skinfold_mm',
                'anterior_thigh_skinfold_mm',
                'medial_calf_skinfold_mm',
                'pectoral_skinfold_mm',
                'skinfold_equation',
                'body_fat_skinfold_percentage',
                'skinfolds_notes',
            ]);

            // ─── 3. COMPOSICIÓN CORPORAL
            $table->dropColumn([
                'fat_mass_kg',
                'muscle_mass_kg',
                'water_percentage',
                'bone_mass_kg',
                'visceral_fat_level',
                'basal_metabolism_kcal',
                'metabolic_age',
                'body_composition_notes',
            ]);
        });
    }
};
