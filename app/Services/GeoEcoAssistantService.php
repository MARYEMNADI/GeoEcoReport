<?php

namespace App\Services;

use App\Models\Incident;

class GeoEcoAssistantService
{
    /**
     * Analyser un incident.
     */
    public function analyze(Incident $incident): array
    {
        return [
            'summary' => $this->generateSummary($incident),
            'suggested_category' => $this->suggestCategory($incident),
            'suggested_priority' => $this->suggestPriority($incident),
        ];
    }

    /**
     * Générer un résumé simple.
     */
    private function generateSummary(Incident $incident): string
    {
        return $incident->title . ' : ' . $incident->description;
    }

    /**
     * Suggérer une catégorie.
     */
    private function suggestCategory(Incident $incident): ?string
    {
        $text = strtolower(
            $incident->title . ' ' . $incident->description
        );

        if (
            str_contains($text, 'eau') ||
            str_contains($text, 'fuite') ||
            str_contains($text, 'pollution')
        ) {
            return 'Environnemental';
        }

        if (
            str_contains($text, 'route') ||
            str_contains($text, 'nid') ||
            str_contains($text, 'éclairage') ||
            str_contains($text, 'lampadaire')
        ) {
            return 'Urbain';
        }

        return null;
    }

    /**
     * Suggérer une priorité.
     */
    private function suggestPriority(Incident $incident): string
    {
        $text = strtolower(
            $incident->title . ' ' . $incident->description
        );

        if (
            str_contains($text, 'dangereux') ||
            str_contains($text, 'majeure') ||
            str_contains($text, 'urgence')
        ) {
            return 'Urgente';
        }

        if (
            str_contains($text, 'important') ||
            str_contains($text, 'majeur')
        ) {
            return 'Élevée';
        }

        return 'Moyenne';
    }
}