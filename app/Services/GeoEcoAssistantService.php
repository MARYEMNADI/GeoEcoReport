<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Category;

class GeoEcoAssistantService
{
    /**
     * Analyser un incident (résumé + catégorie + priorité).
     */
    public function analyze(Incident $incident): array
    {
        return [
            'summary'             => $this->generateSummary($incident),
            'suggested_category'  => $this->suggestCategory($incident),
            'suggested_priority'  => $this->suggestPriority($incident),
        ];
    }

    /**
     * Répondre à une question libre de l'utilisateur.
     */
    public function ask(string $question, ?Incident $incident = null): string
    {
        $question = trim($question);
        $lower    = mb_strtolower($question);

        // Questions liées à un incident précis
        if ($incident) {
            if (str_contains($lower, 'résumé') || str_contains($lower, 'résume') || str_contains($lower, 'summary')) {
                return $this->generateSummary($incident);
            }

            if (str_contains($lower, 'catégorie') || str_contains($lower, 'category')) {
                $cat = $this->suggestCategory($incident);
                return $cat
                    ? "Catégorie suggérée : **{$cat}**."
                    : "Je n'ai pas pu déterminer clairement une catégorie pour cet incident.";
            }

            if (str_contains($lower, 'priorité') || str_contains($lower, 'priority')) {
                return "Priorité suggérée : **" . $this->suggestPriority($incident) . "**.";
            }

            if (str_contains($lower, 'statut') || str_contains($lower, 'status')) {
                return "Le statut actuel de l'incident est : **{$incident->status}**.";
            }
        }

        // Questions générales
        if (str_contains($lower, 'bonjour') || str_contains($lower, 'salut') || str_contains($lower, 'hello')) {
            return "Bonjour ! Je suis l'assistant GeoEcoReport. Comment puis-je vous aider aujourd'hui ?";
        }

        if (str_contains($lower, 'aide') || str_contains($lower, 'help') || str_contains($lower, 'que peux-tu')) {
            return "Je peux vous aider à :\n"
                . "• Analyser un incident (résumé, catégorie, priorité)\n"
                . "• Répondre à des questions sur un incident précis\n"
                . "• Donner des conseils sur le signalement environnemental\n\n"
                . "Posez-moi simplement votre question !";
        }

        if (str_contains($lower, 'comment signaler') || str_contains($lower, 'créer un incident')) {
            return "Pour signaler un incident :\n"
                . "1. Allez dans le menu **Incidents** → **Créer**\n"
                . "2. Remplissez le titre, la description et la localisation\n"
                . "3. Ajoutez des photos si possible\n"
                . "4. Validez — un technicien sera ensuite affecté.";
        }

        // Réponse par défaut
        return "Je n'ai pas bien compris votre question. "
            . "Vous pouvez me demander un résumé, une catégorie ou une priorité pour un incident, "
            . "ou me poser une question générale sur GeoEcoReport.";
    }

    private function generateSummary(Incident $incident): string
    {
        $parts = [];

        $parts[] = "Incident « {$incident->title} »";

        if ($incident->description) {
            $desc = mb_strlen($incident->description) > 180
                ? mb_substr($incident->description, 0, 180) . '…'
                : $incident->description;
            $parts[] = $desc;
        }

        $parts[] = "Statut actuel : {$incident->status}";
        $parts[] = "Priorité : " . ($incident->priority ?? 'Non définie');

        return implode('. ', $parts) . '.';
    }

    private function suggestCategory(Incident $incident): ?string
    {
        $text = mb_strtolower($incident->title . ' ' . $incident->description);

        $rules = [
            'Environnemental' => ['eau', 'fuite', 'pollution', 'déchet', 'ordure', 'poubelle', 'arbre', 'forêt', 'air'],
            'Urbain'          => ['route', 'nid-de-poule', 'éclairage', 'lampadaire', 'trottoir', 'chaussée', 'signalisation'],
            'Sanitaire'       => ['santé', 'hôpital', 'clinique', 'maladie', 'hygiène'],
            'Sécurité'        => ['danger', 'sécurité', 'vol', 'agression', 'incendie'],
        ];

        foreach ($rules as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return $category;
                }
            }
        }

        return null;
    }

    private function suggestPriority(Incident $incident): string
    {
        $text = mb_strtolower($incident->title . ' ' . $incident->description);

        if (str_contains($text, 'dangereux') || str_contains($text, 'urgence') || str_contains($text, 'grave') || str_contains($text, 'incendie')) {
            return 'Urgente';
        }

        if (str_contains($text, 'important') || str_contains($text, 'majeur') || str_contains($text, 'élevé')) {
            return 'Élevée';
        }

        return 'Moyenne';
    }
}