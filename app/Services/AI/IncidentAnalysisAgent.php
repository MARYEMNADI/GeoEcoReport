<?php

namespace App\Services\AI;

use App\Models\Category;
use App\Models\Incident;
use App\Services\OpenAIService;
use RuntimeException;

class IncidentAnalysisAgent
{
    public function __construct(
        private readonly OpenAIService $openAI,
    ) {
    }

    /**
     * Analyse un incident avec OpenAI.
     *
     * @return array{
     *     category: ?string,
     *     priority: ?string,
     *     summary: ?string,
     *     suggested_action: ?string,
     *     category_confidence: float,
     *     priority_confidence: float,
     *     priority_reason: ?string,
     *     source: string
     * }
     */
    public function analyze(Incident $incident): array
    {
        $categories = Category::query()
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->all();

        if (empty($categories)) {
            throw new RuntimeException(
                'No incident categories found.'
            );
        }

        $systemPrompt = $this->buildSystemPrompt($categories);

        $userPrompt = $this->buildUserPrompt($incident);

        $content = $this->openAI->chat([
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            [
                'role' => 'user',
                'content' => $userPrompt,
            ],
        ]);

        $data = $this->decodeResponse($content);

        return $this->validateResponse(
            $data,
            $categories
        );
    }

    /**
     * Build the system instructions.
     *
     * @param array<int, string> $categories
     */
    private function buildSystemPrompt(array $categories): string
    {
        $categoryList = implode("\n- ", $categories);

        return <<<PROMPT
Tu es l'assistant intelligent de GeoEcoReport, une plateforme de signalement des incidents environnementaux et urbains.

Ton rôle est d'analyser un signalement et de retourner une analyse structurée.

CATÉGORIES AUTORISÉES :
- {$categoryList}

PRIORITÉS AUTORISÉES :
- Faible
- Moyenne
- Élevée
- Urgente

RÈGLES :
1. Tu dois choisir UNE seule catégorie parmi les catégories autorisées.
2. Tu dois choisir UNE seule priorité parmi les quatre priorités autorisées.
3. Ne crée jamais une nouvelle catégorie.
4. Utilise exactement le nom de la catégorie fourni dans la liste.
5. Le résumé doit être court, clair et professionnel.
6. L'action proposée doit être concrète et adaptée à l'incident.
7. Explique brièvement pourquoi la priorité choisie est appropriée.
8. Les scores de confiance doivent être compris entre 0 et 1.
9. Retourne UNIQUEMENT un JSON valide.
10. N'ajoute aucun texte avant ou après le JSON.

FORMAT OBLIGATOIRE :

{
  "category": "Nom exact de la catégorie",
  "priority": "Faible|Moyenne|Élevée|Urgente",
  "summary": "Résumé professionnel",
  "suggested_action": "Action recommandée",
  "category_confidence": 0.0,
  "priority_confidence": 0.0,
  "priority_reason": "Raison courte"
}
PROMPT;
    }

    /**
     * Build the incident prompt.
     */
    private function buildUserPrompt(Incident $incident): string
    {
        return <<<PROMPT
Analyse cet incident GeoEcoReport :

Titre :
{$incident->title}

Description :
{$incident->description}

Coordonnées :
Latitude : {$incident->latitude}
Longitude : {$incident->longitude}

Retourne uniquement le JSON demandé.
PROMPT;
    }

    /**
     * Decode the OpenAI JSON response.
     *
     * @return array<string, mixed>
     */
    private function decodeResponse(string $content): array
    {
        $content = trim($content);

        // Handle possible Markdown JSON fences.
        if (str_starts_with($content, '```')) {
            $content = preg_replace(
                '/^```(?:json)?\s*/i',
                '',
                $content
            );

            $content = preg_replace(
                '/\s*```$/',
                '',
                $content
            );

            $content = trim($content);
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new RuntimeException(
                'Invalid JSON returned by OpenAI.'
            );
        }

        return $data;
    }

    /**
     * Validate and normalize the AI response.
     *
     * @param array<string, mixed> $data
     * @param array<int, string> $categories
     * @return array{
     *     category: string,
     *     priority: string,
     *     summary: ?string,
     *     suggested_action: ?string,
     *     category_confidence: float,
     *     priority_confidence: float,
     *     priority_reason: ?string,
     *     source: string
     * }
     */
    private function validateResponse(
        array $data,
        array $categories
    ): array {
        $category = $data['category'] ?? null;
        $priority = $data['priority'] ?? null;

        /*
         * Validate category.
         */
        if (
            !is_string($category)
            || !in_array($category, $categories, true)
        ) {
            throw new RuntimeException(
                'OpenAI returned an invalid incident category.'
            );
        }

        /*
         * Validate priority.
         */
        $allowedPriorities = [
            'Faible',
            'Moyenne',
            'Élevée',
            'Urgente',
        ];

        if (
            !is_string($priority)
            || !in_array($priority, $allowedPriorities, true)
        ) {
            throw new RuntimeException(
                'OpenAI returned an invalid incident priority.'
            );
        }

        /*
         * Normalize confidence scores.
         */
        $categoryConfidence = $this->normalizeConfidence(
            $data['category_confidence'] ?? 0
        );

        $priorityConfidence = $this->normalizeConfidence(
            $data['priority_confidence'] ?? 0
        );

        return [
            'category' => $category,
            'priority' => $priority,

            'summary' => $this->nullableString(
                $data['summary'] ?? null
            ),

            'suggested_action' => $this->nullableString(
                $data['suggested_action'] ?? null
            ),

            'category_confidence' => $categoryConfidence,

            'priority_confidence' => $priorityConfidence,

            'priority_reason' => $this->nullableString(
                $data['priority_reason'] ?? null
            ),

            'source' => 'openai',
        ];
    }

    /**
     * Keep confidence between 0 and 1.
     */
    private function normalizeConfidence(mixed $value): float
    {
        if (!is_numeric($value)) {
            return 0.0;
        }

        return max(
            0.0,
            min(1.0, (float) $value)
        );
    }

    /**
     * Convert a value to nullable string.
     */
    private function nullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}