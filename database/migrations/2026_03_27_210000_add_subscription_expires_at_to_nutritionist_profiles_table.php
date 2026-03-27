<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nutritionist_profiles', function (Blueprint $table) {
            // Fecha de vencimiento de la suscripción de pago.
            // NULL significa plan free (sin vencimiento).
            // Para planes normal/pro: se renueva mensualmente al confirmar pago.
            $table->timestamp('subscription_expires_at')->nullable()->after('subscribed_at');
        });
    }

    public function down(): void
    {
        Schema::table('nutritionist_profiles', function (Blueprint $table) {
            $table->dropColumn('subscription_expires_at');
        });
    }
};
