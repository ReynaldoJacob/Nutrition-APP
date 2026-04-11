<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nutritionist_id')->constrained('users')->cascadeOnDelete();
            $table->string('content_type', 30);
            $table->string('title', 180);
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->unsignedSmallInteger('reading_time_minutes')->default(5);
            $table->string('status', 30)->default('draft');
            $table->string('cover_image_path')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nutritionist_id', 'status']);
            $table->index(['nutritionist_id', 'content_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};
