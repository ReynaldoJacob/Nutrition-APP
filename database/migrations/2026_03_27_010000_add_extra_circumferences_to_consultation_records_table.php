<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_records', function (Blueprint $table) {
            $table->unsignedDecimal('arm_contracted_cm', 5, 2)->nullable()->comment('Perímetro brazo contraído cm')->after('arm_circ_cm');
            $table->unsignedDecimal('abdomen_cm',        5, 2)->nullable()->comment('Perímetro abdomen cm')->after('arm_contracted_cm');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_records', function (Blueprint $table) {
            $table->dropColumn(['arm_contracted_cm', 'abdomen_cm']);
        });
    }
};
