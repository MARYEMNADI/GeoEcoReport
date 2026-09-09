<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(Request $request): View
    {
        $query = Incident::with(['category', 'user'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $incidents = $query->latest()->get();

        // Données légères pour Leaflet
        $incidentsForMap = $incidents->map(function ($incident) {
            return [
                'id'            => $incident->id,
                'title'         => $incident->title,
                'latitude'      => (float) $incident->latitude,
                'longitude'     => (float) $incident->longitude,
                'status'        => $incident->status,
                'category_name' => $incident->category->name ?? null,
            ];
        });

        $categories = Category::orderBy('name')->get();

        return view('map.index', compact('incidents', 'incidentsForMap', 'categories'));
    }
}