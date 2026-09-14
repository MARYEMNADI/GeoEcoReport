<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Category;
use App\Models\Incident;
use App\Models\IncidentImage;
use App\Models\User;
use App\Services\GeoEcoAssistantService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IncidentController extends Controller
{
    use AuthorizesRequests;

    /**
     * GeoEco Assistant Service.
     */
    public function __construct(
        private GeoEcoAssistantService $assistant
    ) {
    }

    /**
     * Afficher tous les incidents avec statistiques.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Incident::class);

        /*
         * ==================================================
         * 1. LISTE DES INCIDENTS
         * ==================================================
         */
        $incidents = Incident::with([
            'category',
            'user',
            'images',
        ])
            ->latest()
            ->paginate(10);

        /*
         * ==================================================
         * 2. STATISTIQUES
         * ==================================================
         */

        // Nombre total d'incidents
        $totalIncidents = Incident::count();

        // Incidents en attente
        $pendingIncidents = Incident::where(
            'status',
            'En attente'
        )->count();

        // Incidents en cours de traitement
        $inProgressIncidents = Incident::where(
            'status',
            'En cours de traitement'
        )->count();

        // Incidents résolus
        $resolvedIncidents = Incident::where(
            'status',
            'Résolu'
        )->count();

        // Incidents rejetés
        $rejectedIncidents = Incident::where(
            'status',
            'Rejeté'
        )->count();

        /*
         * ==================================================
         * 3. TAUX DE RÉSOLUTION
         * ==================================================
         */

        $resolutionRate = $totalIncidents > 0
            ? round(
                ($resolvedIncidents / $totalIncidents) * 100,
                1
            )
            : 0;

        /*
         * ==================================================
         * 4. DONNÉES POUR LA VUE
         * ==================================================
         */

        return view(
            'incidents.index',
            compact(
                'incidents',
                'totalIncidents',
                'pendingIncidents',
                'inProgressIncidents',
                'resolvedIncidents',
                'rejectedIncidents',
                'resolutionRate'
            )
        );
    }

    /**
     * Page de création d'un incident.
     */
    public function create(): View
    {
        $this->authorize('create', Incident::class);

        $categories = Category::orderBy('name')->get();

        return view(
            'incidents.create',
            compact('categories')
        );
    }

    /**
     * Enregistrer un nouvel incident.
     *
     * L'utilisateur peut laisser la catégorie vide.
     * GeoEco Assistant propose alors automatiquement
     * une catégorie et une priorité.
     */
    public function store(
        StoreIncidentRequest $request
    ): RedirectResponse {
        $this->authorize('create', Incident::class);

        try {
            /*
             * ==================================================
             * 1. DONNÉES VALIDÉES
             * ==================================================
             */
            $data = $request->validated();

            /*
             * L'image est traitée séparément.
             */
            unset($data['image']);

            /*
             * ==================================================
             * 2. DONNÉES SYSTÈME
             * ==================================================
             */
            $data['user_id'] = auth()->id();
            $data['status'] = 'En attente';

            /*
             * Priorité temporaire pour l'analyse.
             */
            $data['priority'] = 'Moyenne';

            /*
             * ==================================================
             * 3. INCIDENT TEMPORAIRE
             * ==================================================
             */
            $temporaryIncident = new Incident();

            $temporaryIncident->fill($data);

            /*
             * ==================================================
             * 4. ANALYSE GEOECO ASSISTANT
             * ==================================================
             */
            $analysis = $this->assistant->analyze(
                $temporaryIncident
            );

            /*
             * ==================================================
             * 5. RÉSULTATS DE L'ANALYSE
             * ==================================================
             */

            $suggestedCategoryName =
                $analysis['suggested_category']
                ?? $analysis['category']
                ?? null;

            $suggestedPriority =
                $analysis['suggested_priority']
                ?? $analysis['priority']
                ?? 'Moyenne';

            $summary =
                $analysis['summary']
                ?? null;

            $suggestedAction =
                $analysis['suggested_action']
                ?? $analysis['ai_suggested_action']
                ?? null;

            $categoryConfidence =
                $analysis['category_confidence']
                ?? $analysis['ai_category_confidence']
                ?? null;

            $priorityConfidence =
                $analysis['priority_confidence']
                ?? $analysis['ai_priority_confidence']
                ?? null;

            $priorityReason =
                $analysis['priority_reason']
                ?? $analysis['ai_priority_reason']
                ?? null;

            $aiSource =
                $analysis['source']
                ?? $analysis['ai_source']
                ?? 'rule_based';

            /*
             * ==================================================
             * 6. DÉTERMINER LA CATÉGORIE
             * ==================================================
             */

            $category = null;

            /*
             * Catégorie choisie manuellement.
             */
            if (!empty($data['category_id'])) {
                $category = Category::find(
                    $data['category_id']
                );
            }

            /*
             * Catégorie proposée automatiquement.
             */
            if (!$category && $suggestedCategoryName) {
                $category = Category::where(
                    'name',
                    $suggestedCategoryName
                )->first();
            }

            /*
             * ==================================================
             * 7. AUCUNE CATÉGORIE TROUVÉE
             * ==================================================
             */

            if (!$category) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'category_id' =>
                            "GeoEco Assistant n'a pas pu déterminer automatiquement la catégorie. Veuillez sélectionner une catégorie manuellement.",
                    ]);
            }

            /*
             * ==================================================
             * 8. DONNÉES FINALES
             * ==================================================
             */

            $data['category_id'] = $category->id;

            $data['priority'] = $suggestedPriority;

            $data['ai_summary'] = $summary;

            $data['ai_suggested_category'] =
                $suggestedCategoryName;

            $data['ai_category_confidence'] =
                $categoryConfidence;

            $data['ai_priority_confidence'] =
                $priorityConfidence;

            $data['ai_priority_reason'] =
                $priorityReason;

            $data['ai_suggested_action'] =
                $suggestedAction;

            $data['ai_source'] =
                $aiSource;

            /*
             * ==================================================
             * 9. CRÉER L'INCIDENT
             * ==================================================
             */

            $incident = Incident::create($data);

            /*
             * ==================================================
             * 10. UPLOAD IMAGE
             * ==================================================
             */

            if ($request->hasFile('image')) {
                $path = $request
                    ->file('image')
                    ->store(
                        'incidents',
                        'public'
                    );

                $incident->images()->create([
                    'image_path' => $path,
                ]);
            }

            /*
             * ==================================================
             * 11. REDIRECTION
             * ==================================================
             */

            return redirect()
                ->route(
                    'incidents.show',
                    $incident
                )
                ->with(
                    'success',
                    "Incident signalé avec succès. GeoEco Assistant a analysé l'incident."
                );

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    "Une erreur est survenue lors de la création de l'incident : "
                    . $e->getMessage()
                );
        }
    }

    /**
     * Afficher un incident.
     */
    public function show(
        Incident $incident
    ): View {
        $this->authorize(
            'view',
            $incident
        );

        /*
         * Charger toutes les relations nécessaires.
         */
        $incident->load([
            'category',
            'user',
            'images',
            'comments.user',
            'affectations.technicien',
        ]);

        /*
         * Récupérer les techniciens.
         */
        $techniciens = User::whereHas(
            'roles',
            function ($query) {
                $query->where(
                    'name',
                    'technicien'
                );
            }
        )
            ->orderBy('name')
            ->get();

        return view(
            'incidents.show',
            compact(
                'incident',
                'techniciens'
            )
        );
    }

    /**
     * Page de modification.
     */
    public function edit(
        Incident $incident
    ): View {
        $this->authorize(
            'update',
            $incident
        );

        /*
         * Récupérer les catégories.
         */
        $categories = Category::orderBy('name')->get();

        /*
         * Charger les images.
         */
        $incident->load('images');

        return view(
            'incidents.edit',
            compact(
                'incident',
                'categories'
            )
        );
    }

    /**
     * Modifier un incident.
     *
     * Après modification, GeoEco Assistant
     * réanalyse automatiquement l'incident.
     */
    public function update(
        UpdateIncidentRequest $request,
        Incident $incident
    ): RedirectResponse {
        $this->authorize(
            'update',
            $incident
        );

        try {
            /*
             * ==================================================
             * 1. DONNÉES VALIDÉES
             * ==================================================
             */
            $data = $request->validated();

            /*
             * Image traitée séparément.
             */
            unset($data['image']);

            /*
             * ==================================================
             * 2. CATÉGORIE MANUELLE
             * ==================================================
             */

            $manualCategoryId =
                $data['category_id']
                ?? null;

            /*
             * ==================================================
             * 3. METTRE À JOUR LES DONNÉES
             * ==================================================
             */

            $incident->update($data);

            /*
             * Recharger le modèle.
             */
            $incident->refresh();

            /*
             * ==================================================
             * 4. ANALYSE GEOECO ASSISTANT
             * ==================================================
             */

            $analysis = $this->assistant->analyze(
                $incident
            );

            /*
             * ==================================================
             * 5. RÉSULTATS DE L'ANALYSE
             * ==================================================
             */

            $suggestedCategoryName =
                $analysis['suggested_category']
                ?? $analysis['category']
                ?? null;

            $suggestedPriority =
                $analysis['suggested_priority']
                ?? $analysis['priority']
                ?? 'Moyenne';

            $summary =
                $analysis['summary']
                ?? null;

            $suggestedAction =
                $analysis['suggested_action']
                ?? $analysis['ai_suggested_action']
                ?? null;

            $categoryConfidence =
                $analysis['category_confidence']
                ?? $analysis['ai_category_confidence']
                ?? null;

            $priorityConfidence =
                $analysis['priority_confidence']
                ?? $analysis['ai_priority_confidence']
                ?? null;

            $priorityReason =
                $analysis['priority_reason']
                ?? $analysis['ai_priority_reason']
                ?? null;

            $aiSource =
                $analysis['source']
                ?? $analysis['ai_source']
                ?? 'rule_based';

            /*
             * ==================================================
             * 6. DÉTERMINER LA CATÉGORIE
             * ==================================================
             */

            $category = null;

            /*
             * Catégorie manuelle.
             */
            if (!empty($manualCategoryId)) {
                $category = Category::find(
                    $manualCategoryId
                );
            }

            /*
             * Catégorie automatique.
             */
            if (!$category && $suggestedCategoryName) {
                $category = Category::where(
                    'name',
                    $suggestedCategoryName
                )->first();
            }

            /*
             * ==================================================
             * 7. DONNÉES GEOECO
             * ==================================================
             */

            $updateData = [
                'ai_summary' =>
                    $summary,

                'ai_suggested_category' =>
                    $suggestedCategoryName,

                'ai_category_confidence' =>
                    $categoryConfidence,

                'ai_priority_confidence' =>
                    $priorityConfidence,

                'ai_priority_reason' =>
                    $priorityReason,

                'ai_suggested_action' =>
                    $suggestedAction,

                'ai_source' =>
                    $aiSource,

                'priority' =>
                    $suggestedPriority,
            ];

            /*
             * Si une catégorie a été trouvée,
             * on l'enregistre.
             */
            if ($category) {
                $updateData['category_id'] =
                    $category->id;
            }

            /*
             * ==================================================
             * 8. SAUVEGARDER
             * ==================================================
             */

            $incident->update(
                $updateData
            );

            /*
             * ==================================================
             * 9. AJOUTER UNE IMAGE
             * ==================================================
             */

            if ($request->hasFile('image')) {
                $path = $request
                    ->file('image')
                    ->store(
                        'incidents',
                        'public'
                    );

                $incident->images()->create([
                    'image_path' => $path,
                ]);
            }

            /*
             * ==================================================
             * 10. REDIRECTION
             * ==================================================
             */

            return redirect()
                ->route(
                    'incidents.show',
                    $incident
                )
                ->with(
                    'success',
                    "Incident modifié et réanalysé par GeoEco Assistant."
                );

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    "Une erreur est survenue lors de la modification de l'incident : "
                    . $e->getMessage()
                );
        }
    }

    /**
     * Supprimer une image.
     */
    public function destroyImage(
        Incident $incident,
        IncidentImage $image
    ): RedirectResponse {
        /*
         * Vérifier que l'image appartient
         * réellement à cet incident.
         */
        if ($image->incident_id !== $incident->id) {
            abort(404);
        }

        /*
         * Vérifier les permissions.
         */
        $this->authorize(
            'update',
            $incident
        );

        /*
         * Supprimer le fichier physique.
         */
        if (
            $image->image_path &&
            Storage::disk('public')->exists(
                $image->image_path
            )
        ) {
            Storage::disk('public')->delete(
                $image->image_path
            );
        }

        /*
         * Supprimer l'enregistrement DB.
         */
        $image->delete();

        return redirect()
            ->route(
                'incidents.show',
                $incident
            )
            ->with(
                'success',
                'La photo a été supprimée avec succès.'
            );
    }

    /**
     * Supprimer un incident.
     */
    public function destroy(
        Incident $incident
    ): RedirectResponse {
        $this->authorize(
            'delete',
            $incident
        );

        /*
         * ==================================================
         * 1. SUPPRIMER LES FICHIERS IMAGES
         * ==================================================
         */

        foreach ($incident->images as $image) {
            if (
                $image->image_path &&
                Storage::disk('public')->exists(
                    $image->image_path
                )
            ) {
                Storage::disk('public')->delete(
                    $image->image_path
                );
            }
        }

        /*
         * ==================================================
         * 2. SUPPRIMER L'INCIDENT
         * ==================================================
         */

        $incident->delete();

        /*
         * ==================================================
         * 3. REDIRECTION
         * ==================================================
         */

        return redirect()
            ->route('incidents.index')
            ->with(
                'success',
                'Incident supprimé avec succès.'
            );
    }
}