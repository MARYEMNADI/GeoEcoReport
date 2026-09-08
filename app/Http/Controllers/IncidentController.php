<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Category;
use App\Models\Incident;
use App\Models\IncidentImage;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IncidentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Afficher tous les incidents.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Incident::class);

        $incidents = Incident::with([
            'category',
            'user',
            'images',
        ])
            ->latest()
            ->paginate(10);

        return view(
            'incidents.index',
            compact('incidents')
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
     */
    public function store(
        StoreIncidentRequest $request
    ): RedirectResponse {

        $this->authorize('create', Incident::class);

        $data = $request->validated();

        $data['user_id'] = auth()->id();
        $data['status'] = 'En attente';
        $data['priority'] = 'Moyenne';

        $incident = Incident::create($data);

        /*
         * Upload de l'image.
         */
        if ($request->hasFile('image')) {

            $path = $request->file('image')
                ->store('incidents', 'public');

            $incident->images()->create([
                'image_path' => $path,
            ]);
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with(
                'success',
                'Incident signalé avec succès.'
            );
    }

    /**
     * Afficher un incident.
     */
    public function show(
        Incident $incident
    ): View {

        $this->authorize('view', $incident);

        $incident->load([
            'category',
            'user',
            'images',
            'comments.user',
            'affectations.technicien',
        ]);

        $techniciens = User::whereHas(
            'roles',
            fn ($query) => $query->where(
                'name',
                'technicien'
            )
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

        $categories = Category::orderBy('name')->get();

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
     */
    public function update(
        UpdateIncidentRequest $request,
        Incident $incident
    ): RedirectResponse {

        $this->authorize(
            'update',
            $incident
        );

        $data = $request->validated();

        $incident->update($data);

        /*
         * Ajouter une nouvelle image.
         */
        if ($request->hasFile('image')) {

            $path = $request->file('image')
                ->store('incidents', 'public');

            $incident->images()->create([
                'image_path' => $path,
            ]);
        }

        return redirect()
            ->route(
                'incidents.show',
                $incident
            )
            ->with(
                'success',
                'Incident modifié avec succès.'
            );
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
         * bien à cet incident.
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
         * Supprimer l'enregistrement
         * de la base de données.
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
         * Supprimer toutes les images
         * liées à l'incident.
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
         * Supprimer l'incident.
         */
        $incident->delete();

        return redirect()
            ->route('incidents.index')
            ->with(
                'success',
                'Incident supprimé avec succès.'
            );
    }
}