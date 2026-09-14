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

        // Recherche par titre ou description
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtre par priorité
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $incidents = $query
            ->latest()
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('map.index', compact('incidents', 'categories'));
    }
}