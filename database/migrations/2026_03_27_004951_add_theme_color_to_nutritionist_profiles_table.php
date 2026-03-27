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
        Schema::table('nutritionist_profiles', function (Blueprint $table) {
            $table->string('theme_color', 20)->default('emerald')->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nutritionist_profiles', function (Blueprint $table) {
            $table->dropColumn('theme_color');
        });
    }
};
