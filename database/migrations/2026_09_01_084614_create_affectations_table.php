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
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();

            // Incident affecté
            $table->foreignId('incident_id')
                ->constrained('incidents')
                ->cascadeOnDelete();

            // Technicien affecté
            $table->foreignId('technicien_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Informations sur l'affectation
            $table->timestamp('date_affectation')
                ->useCurrent();

            $table->text('instructions')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
