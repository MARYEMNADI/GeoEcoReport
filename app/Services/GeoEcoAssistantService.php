<?php

namespace App\Services;

use App\Models\Incident;
use App\Services\AI\IncidentAnalysisAgent;
use Throwable;

class GeoEcoAssistantService
{
    public function __construct(
        private readonly IncidentAnalysisAgent $aiAgent,
    ) {
    }

    /**
     * Analyse complète d'un incident.
     *
     * OpenAI est utilisé en priorité.
     * En cas d'erreur, on revient automatiquement
     * vers le système Rule-Based existant.
     */
    public function analyze(Incident $incident): array
    {
        /*
         * ==========================================
         * OPENAI / AI AGENT
         * ==========================================
         */
        try {

            $aiResult = $this->aiAgent->analyze($incident);

            return [
                'summary' =>
                    $aiResult['summary']
                    ?? $this->generateSummary(
                        $incident,
                        $aiResult['priority'] ?? 'Moyenne'
                    ),

                'suggested_category' =>
                    $aiResult['category'] ?? null,

                'suggested_priority' =>
                    $aiResult['priority'] ?? 'Moyenne',

                'suggested_action' =>
                    $aiResult['suggested_action']
                    ?? $this->suggestActionRuleBased($incident),

                'improved_description' =>
                    $this->improveDescriptionRuleBased($incident),

                /*
                 * AI metadata
                 */
                'ai_category_confidence' =>
                    $aiResult['category_confidence'] ?? 0.0,

                'ai_priority_confidence' =>
                    $aiResult['priority_confidence'] ?? 0.0,

                'ai_priority_reason' =>
                    $aiResult['priority_reason'] ?? null,

                'ai_suggested_action' =>
                    $aiResult['suggested_action'] ?? null,

                'ai_source' =>
                    'openai',
            ];

        } catch (Throwable $e) {

            /*
             * ==========================================
             * FALLBACK RULE-BASED
             * ==========================================
             *
             * إذا OpenAI ما خدمش، التطبيق يبقى خدام
             * بالـ système Rule-Based القديم.
             */

            return $this->analyzeRuleBased($incident);
        }
    }

    /**
     * Analyse Rule-Based de secours.
     */
    private function analyzeRuleBased(Incident $incident): array
    {
        try {

            /*
             * نحسبو الأول category و priority
             * باش الـsummary يستعمل النتائج الصحيحة.
             */
            $suggestedCategory =
                $this->suggestCategoryRuleBased($incident);

            $suggestedPriority =
                $this->suggestPriorityRuleBased($incident);

            $suggestedAction =
                $this->suggestActionRuleBased($incident);

            return [
                'summary' =>
                    $this->generateSummary(
                        $incident,
                        $suggestedPriority
                    ),

                'suggested_category' =>
                    $suggestedCategory,

                'suggested_priority' =>
                    $suggestedPriority,

                'suggested_action' =>
                    $suggestedAction,

                'improved_description' =>
                    $this->improveDescriptionRuleBased($incident),

                /*
                 * AI metadata
                 */
                'ai_category_confidence' => null,

                'ai_priority_confidence' => null,

                'ai_priority_reason' => null,

                'ai_suggested_action' => $suggestedAction,

                'ai_source' => 'rule_based',
            ];

        } catch (Throwable $e) {

            return [
                'summary' =>
                    $this->generateSummary(
                        $incident,
                        'Moyenne'
                    ),

                'suggested_category' => null,

                'suggested_priority' => 'Moyenne',

                'suggested_action' =>
                    'Vérifier l’incident et effectuer une intervention si nécessaire.',

                'improved_description' =>
                    $incident->description,

                /*
                 * AI metadata
                 */
                'ai_category_confidence' => null,

                'ai_priority_confidence' => null,

                'ai_priority_reason' => null,

                'ai_suggested_action' =>
                    'Vérifier l’incident et effectuer une intervention si nécessaire.',

                'ai_source' => 'rule_based',
            ];
        }
    }

    /**
     * Répondre aux questions de l'utilisateur.
     *
     * Cette partie conserve le comportement Rule-Based
     * existant pour éviter de casser l'assistant actuel.
     */
    public function ask(
        string $question,
        ?Incident $incident = null
    ): string {

        $question = trim($question);

        $lower = mb_strtolower($question);

        /*
         * ==========================================
         * QUESTIONS SUR UN INCIDENT
         * ==========================================
         */

        if ($incident) {

            /*
             * Résumé
             */
            if (
                str_contains($lower, 'résumé') ||
                str_contains($lower, 'résume') ||
                str_contains($lower, 'resume') ||
                str_contains($lower, 'summary')
            ) {

                return $this->generateSummary(
                    $incident,
                    $this->suggestPriorityRuleBased($incident)
                );
            }

            /*
             * Catégorie
             */
            if (
                str_contains($lower, 'catégorie') ||
                str_contains($lower, 'categorie') ||
                str_contains($lower, 'category')
            ) {

                $category =
                    $this->suggestCategoryRuleBased($incident);

                if ($category) {

                    return
                        "Catégorie suggérée : **{$category}**.";
                }

                return
                    "Je n'ai pas pu déterminer clairement une catégorie pour cet incident.";
            }

            /*
             * Priorité
             */
            if (
                str_contains($lower, 'priorité') ||
                str_contains($lower, 'priorite') ||
                str_contains($lower, 'priority')
            ) {

                return
                    "Priorité suggérée : **" .
                    $this->suggestPriorityRuleBased($incident) .
                    "**.";
            }

            /*
             * Action
             */
            if (
                str_contains($lower, 'action') ||
                str_contains($lower, 'intervention') ||
                str_contains($lower, 'que faire') ||
                str_contains($lower, 'conseil')
            ) {

                return
                    $this->suggestActionRuleBased($incident);
            }

            /*
             * Description
             */
            if (
                str_contains($lower, 'description') ||
                str_contains($lower, 'améliorer') ||
                str_contains($lower, 'ameliore')
            ) {

                return
                    $this->improveDescriptionRuleBased($incident);
            }

            /*
             * Statut
             */
            if (
                str_contains($lower, 'statut') ||
                str_contains($lower, 'status')
            ) {

                return
                    "Le statut actuel de l'incident est : **" .
                    ($incident->status ?? 'Non défini') .
                    "**.";
            }
        }

        /*
         * ==========================================
         * QUESTIONS GÉNÉRALES
         * ==========================================
         */

        if (
            str_contains($lower, 'bonjour') ||
            str_contains($lower, 'salut') ||
            str_contains($lower, 'hello')
        ) {

            return
                "Bonjour ! Je suis l'assistant GeoEcoReport. " .
                "Comment puis-je vous aider aujourd'hui ?";
        }

        if (
            str_contains($lower, 'aide') ||
            str_contains($lower, 'help') ||
            str_contains($lower, 'que peux-tu')
        ) {

            return
                "Je peux vous aider à :\n" .
                "• Analyser un incident\n" .
                "• Suggérer une catégorie\n" .
                "• Déterminer une priorité\n" .
                "• Proposer une action\n" .
                "• Améliorer une description\n" .
                "• Répondre à des questions sur GeoEcoReport.";
        }

        if (
            str_contains($lower, 'comment signaler') ||
            str_contains($lower, 'créer un incident') ||
            str_contains($lower, 'creer un incident')
        ) {

            return
                "Pour signaler un incident :\n" .
                "1. Allez dans **Incidents → Créer**.\n" .
                "2. Saisissez le titre et la description.\n" .
                "3. Choisissez une catégorie ou laissez l'assistant la déterminer.\n" .
                "4. Choisissez la localisation sur la carte.\n" .
                "5. Ajoutez une photo si nécessaire.\n" .
                "6. Cliquez sur **Signaler l'incident**.";
        }

        return
            "Je n'ai pas bien compris votre question. " .
            "Vous pouvez me demander un résumé, une catégorie, " .
            "une priorité ou une action pour un incident.";
    }

    /**
     * Générer un résumé.
     *
     * La priorité passée en paramètre est la priorité
     * calculée par l'assistant.
     */
    private function generateSummary(
        Incident $incident,
        ?string $suggestedPriority = null
    ): string {

        $parts = [];

        /*
         * Titre
         */
        if ($incident->title) {

            $parts[] =
                "Incident « {$incident->title} »";
        }

        /*
         * Description
         */
        if ($incident->description) {

            $description =
                trim($incident->description);

            if (mb_strlen($description) > 200) {

                $description =
                    mb_substr(
                        $description,
                        0,
                        200
                    ) . '…';
            }

            $parts[] = $description;
        }

        /*
         * Statut
         */
        if ($incident->status) {

            $parts[] =
                "Statut actuel : {$incident->status}";
        }

        /*
         * Priorité
         */
        $priority =
            $suggestedPriority
            ?? $incident->priority
            ?? 'Non définie';

        $parts[] =
            "Priorité : {$priority}";

        return implode('. ', $parts) . '.';
    }

    /**
     * Déterminer automatiquement la catégorie.
     *
     * Rule-Based version.
     */
    private function suggestCategoryRuleBased(
        Incident $incident
    ): ?string {

        $text =
            mb_strtolower(
                ($incident->title ?? '') . ' ' .
                ($incident->description ?? '')
            );

        /*
         * Normaliser les apostrophes.
         */
        $text = str_replace(
            [
                '’',
                '‘',
                '`',
            ],
            "'",
            $text
        );

        /*
         * ==========================================
         * Fuite d'eau
         * ==========================================
         */
        if (
            str_contains($text, 'fuite') ||
            str_contains($text, "fuite d'eau") ||
            str_contains($text, 'fuite eau') ||
            str_contains($text, 'eau qui coule') ||
            str_contains($text, 'canalisation')
        ) {

            return "Fuite d’eau";
        }

        /*
         * ==========================================
         * Pollution de l'eau
         * ==========================================
         */
        if (
            str_contains(
                $text,
                "pollution de l'eau"
            ) ||
            str_contains(
                $text,
                'pollution eau'
            ) ||
            str_contains(
                $text,
                'eau polluée'
            ) ||
            str_contains(
                $text,
                'eau polluee'
            ) ||
            str_contains(
                $text,
                'pollution hydrique'
            )
        ) {

            return "Pollution de l’eau";
        }

        /*
         * ==========================================
         * Pollution de l'air
         * ==========================================
         */
        if (
            str_contains(
                $text,
                "pollution de l'air"
            ) ||
            str_contains(
                $text,
                'pollution air'
            ) ||
            str_contains(
                $text,
                'fumée'
            ) ||
            str_contains(
                $text,
                'fumee'
            ) ||
            str_contains(
                $text,
                'gaz toxique'
            ) ||
            str_contains(
                $text,
                'fumée noire'
            ) ||
            str_contains(
                $text,
                'fumee noire'
            )
        ) {

            return "Pollution de l’air";
        }

        /*
         * ==========================================
         * Pollution sonore
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'pollution sonore'
            ) ||
            str_contains(
                $text,
                'bruit excessif'
            ) ||
            str_contains(
                $text,
                'nuisance sonore'
            ) ||
            str_contains(
                $text,
                'bruit'
            )
        ) {

            return "Pollution sonore";
        }

        /*
         * ==========================================
         * Déchets ménagers
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'déchet ménager'
            ) ||
            str_contains(
                $text,
                'dechet menager'
            ) ||
            str_contains(
                $text,
                'ordures ménagères'
            ) ||
            str_contains(
                $text,
                'ordures menageres'
            ) ||
            str_contains(
                $text,
                'poubelle'
            )
        ) {

            return "Déchets ménagers";
        }

        /*
         * ==========================================
         * Dépôt sauvage de déchets
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'dépôt sauvage'
            ) ||
            str_contains(
                $text,
                'depot sauvage'
            ) ||
            str_contains(
                $text,
                'décharge sauvage'
            ) ||
            str_contains(
                $text,
                'decharge sauvage'
            ) ||
            str_contains(
                $text,
                'déchets abandonnés'
            ) ||
            str_contains(
                $text,
                'dechets abandonnes'
            )
        ) {

            return "Dépôt sauvage de déchets";
        }

        /*
         * ==========================================
         * Coupe illégale d'arbres
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'coupe illégale'
            ) ||
            str_contains(
                $text,
                'coupe illegale'
            ) ||
            str_contains(
                $text,
                'abattage'
            ) ||
            str_contains(
                $text,
                'arbre coupé'
            ) ||
            str_contains(
                $text,
                'arbre coupe'
            )
        ) {

            return "Coupe illégale d’arbres";
        }

        /*
         * ==========================================
         * Dégradation des espaces verts
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'espace vert'
            ) ||
            str_contains(
                $text,
                'espaces verts'
            ) ||
            str_contains(
                $text,
                'dégradation des espaces verts'
            ) ||
            str_contains(
                $text,
                'degradation des espaces verts'
            ) ||
            str_contains(
                $text,
                'jardin dégradé'
            ) ||
            str_contains(
                $text,
                'jardin degrade'
            )
        ) {

            return "Dégradation des espaces verts";
        }

        /*
         * ==========================================
         * Éclairage public défectueux
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'éclairage public'
            ) ||
            str_contains(
                $text,
                'eclairage public'
            ) ||
            str_contains(
                $text,
                'lampadaire'
            ) ||
            str_contains(
                $text,
                'lampes publiques'
            ) ||
            str_contains(
                $text,
                'lumière publique'
            ) ||
            str_contains(
                $text,
                'lumiere publique'
            )
        ) {

            return "Éclairage public défectueux";
        }

        /*
         * ==========================================
         * Incendie
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'incendie'
            ) ||
            str_contains(
                $text,
                'feu'
            ) ||
            str_contains(
                $text,
                'flamme'
            )
        ) {

            return "Incendie";
        }

        /*
         * ==========================================
         * Nids-de-poule
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'nid-de-poule'
            ) ||
            str_contains(
                $text,
                'nid de poule'
            ) ||
            str_contains(
                $text,
                'nids-de-poule'
            ) ||
            str_contains(
                $text,
                'trou dans la route'
            ) ||
            str_contains(
                $text,
                'trou sur la route'
            ) ||
            str_contains(
                $text,
                'route dégradée'
            ) ||
            str_contains(
                $text,
                'route degradee'
            )
        ) {

            return "Nids-de-poule";
        }

        /*
         * ==========================================
         * Signalisation endommagée
         * ==========================================
         */
        if (
            str_contains(
                $text,
                'signalisation'
            ) ||
            str_contains(
                $text,
                'panneau'
            ) ||
            str_contains(
                $text,
                'panneau cassé'
            ) ||
            str_contains(
                $text,
                'panneau casse'
            )
        ) {

            return "Signalisation endommagée";
        }

        /*
         * Aucune catégorie détectée.
         */
        return null;
    }

    /**
     * Déterminer automatiquement la priorité.
     *
     * Rule-Based version.
     */
    private function suggestPriorityRuleBased(
        Incident $incident
    ): string {

        $text =
            mb_strtolower(
                ($incident->title ?? '') . ' ' .
                ($incident->description ?? '')
            );

        /*
         * Normaliser les apostrophes.
         */
        $text = str_replace(
            [
                '’',
                '‘',
                '`',
            ],
            "'",
            $text
        );

        /*
         * ==========================================
         * PRIORITÉ URGENTE
         * ==========================================
         */
        $urgentKeywords = [

            'urgence',
            'urgent',
            'urgente',

            'dangereux',
            'dangereuse',

            'danger',
            'danger immédiat',
            'danger immediat',

            'risque immédiat',
            'risque immediat',

            'grave',

            'incendie',

            'explosion',

            'accident',

            'blessé',
            'blesse',

            'menace',
            'menace pour',

            'danger pour',
        ];

        foreach ($urgentKeywords as $keyword) {

            if (
                str_contains(
                    $text,
                    $keyword
                )
            ) {

                return 'Urgente';
            }
        }

        /*
         * ==========================================
         * PRIORITÉ ÉLEVÉE
         * ==========================================
         */
        $highKeywords = [

            'important',
            'importante',

            'majeur',
            'majeure',

            'élevé',
            'élevée',

            'eleve',
            'elevee',

            'forte fuite',
            'grande fuite',

            'risque',
        ];

        foreach ($highKeywords as $keyword) {

            if (
                str_contains(
                    $text,
                    $keyword
                )
            ) {

                return 'Élevée';
            }
        }

        /*
         * ==========================================
         * PRIORITÉ FAIBLE
         * ==========================================
         */
        $lowKeywords = [

            'léger',
            'légère',

            'leger',
            'legere',

            'petit',
            'petite',

            'mineur',
            'mineure',

            'faible',
        ];

        foreach ($lowKeywords as $keyword) {

            if (
                str_contains(
                    $text,
                    $keyword
                )
            ) {

                return 'Faible';
            }
        }

        /*
         * ==========================================
         * PRIORITÉ PAR DÉFAUT
         * ==========================================
         */
        return 'Moyenne';
    }

    /**
     * Proposer une action.
     *
     * Rule-Based version.
     */
    private function suggestActionRuleBased(
        Incident $incident
    ): string {

        $category =
            $this->suggestCategoryRuleBased($incident);

        switch ($category) {

            case "Fuite d’eau":

                return
                    "Vérifier rapidement la fuite, " .
                    "sécuriser la zone et contacter le service " .
                    "responsable du réseau d'eau.";

            case "Pollution de l’eau":

                return
                    "Identifier la source de pollution, " .
                    "sécuriser la zone et prévenir le service " .
                    "environnemental compétent.";

            case "Pollution de l’air":

                return
                    "Identifier la source des émissions, " .
                    "évaluer le risque pour les habitants " .
                    "et prévenir le service compétent.";

            case "Pollution sonore":

                return
                    "Identifier la source du bruit et vérifier " .
                    "si elle dépasse les niveaux autorisés.";

            case "Déchets ménagers":

                return
                    "Organiser le ramassage des déchets " .
                    "et nettoyer la zone concernée.";

            case "Dépôt sauvage de déchets":

                return
                    "Sécuriser la zone, documenter le dépôt " .
                    "et organiser son nettoyage.";

            case "Coupe illégale d’arbres":

                return
                    "Constater les dégâts, documenter la situation " .
                    "et prévenir le service environnemental compétent.";

            case "Dégradation des espaces verts":

                return
                    "Évaluer les dégâts et planifier une intervention " .
                    "pour restaurer l'espace vert.";

            case "Éclairage public défectueux":

                return
                    "Vérifier l'équipement défectueux et transmettre " .
                    "une demande d'intervention au service technique.";

            case "Incendie":

                return
                    "Alerter immédiatement les services d'urgence " .
                    "et éviter l'accès à la zone dangereuse.";

            case "Nids-de-poule":

                return
                    "Sécuriser la zone et transmettre une demande " .
                    "de réparation de la chaussée.";

            case "Signalisation endommagée":

                return
                    "Vérifier le panneau ou la signalisation " .
                    "et programmer son remplacement ou sa réparation.";

            default:

                return
                    "Vérifier l'incident sur place et déterminer " .
                    "l'intervention appropriée.";
        }
    }

    /**
     * Améliorer la description.
     *
     * Rule-Based version.
     */
    private function improveDescriptionRuleBased(
        Incident $incident
    ): string {

        $category =
            $this->suggestCategoryRuleBased($incident);

        $priority =
            $this->suggestPriorityRuleBased($incident);

        $description =
            trim(
                $incident->description ?? ''
            );

        if (!$description) {

            return
                "Aucune description n'a été fournie.";
        }

        return
            "Description améliorée : " .
            $description .
            "\n\nCatégorie détectée : " .
            ($category ?? 'Non déterminée') .
            "\nPriorité estimée : " .
            $priority . ".";
    }
}