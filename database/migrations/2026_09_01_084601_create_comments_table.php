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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // Contenu du commentaire
            $table->text('content');

            // Incident concerné
            $table->foreignId('incident_id')
                ->constrained('incidents')
                ->cascadeOnDelete();

            // Utilisateur qui a écrit le commentaire
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
