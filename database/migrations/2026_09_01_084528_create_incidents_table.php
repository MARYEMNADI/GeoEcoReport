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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();

            // Informations de l'incident
            $table->string('title');
            $table->text('description');

            // Géolocalisation
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Workflow
            $table->enum('status', [
                'En attente',
                'En cours de traitement',
                'Résolu',
                'Rejeté'
            ])->default('En attente');

            // Priorité
            $table->enum('priority', [
                'Faible',
                'Moyenne',
                'Élevée',
                'Urgente'
            ])->default('Moyenne');

            // Relations
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            // Données générées par GeoEco Assistant
            $table->text('ai_summary')->nullable();
            $table->string('ai_suggested_category', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
