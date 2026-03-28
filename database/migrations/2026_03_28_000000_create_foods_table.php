<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('source', 32)->default('usda');
            $table->string('external_id', 64);
            $table->string('name');
            $table->string('brand')->nullable();
            $table->decimal('serving_size', 8, 2)->nullable();
            $table->string('serving_unit', 32)->nullable();
            $table->decimal('calories', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('carbs', 8, 2)->default(0);
            $table->decimal('fat', 8, 2)->default(0);
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->unique(['source', 'external_id']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
