<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nutritionist_profiles', function (Blueprint $table) {
            $table->string('subscription_plan_key', 30)->default('free')->after('clinic_logo_path');
            $table->timestamp('subscribed_at')->nullable()->after('subscription_plan_key');
        });
    }

    public function down(): void
    {
        Schema::table('nutritionist_profiles', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan_key', 'subscribed_at']);
        });
    }
};
