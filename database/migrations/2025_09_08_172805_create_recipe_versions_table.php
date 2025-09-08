<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipe_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->integer('version_number');
            $table->string('name');
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->json('ingredients');
            $table->json('instructions');
            $table->integer('prep_time')->nullable();
            $table->integer('cook_time')->nullable();
            $table->integer('servings')->default(1);
            $table->string('image_url')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->unique(['recipe_id', 'version_number']);
            $table->index(['recipe_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_versions');
    }
};
