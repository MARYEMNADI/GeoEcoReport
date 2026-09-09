<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard Citoyen
     */
    public function citoyen(): View
    {
        $user = auth()->user();

        $incidents = Incident::with('category')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total'      => Incident::where('user_id', $user->id)->count(),
            'en_attente' => Incident::where('user_id', $user->id)->where('status', 'En attente')->count(),
            'en_cours'   => Incident::where('user_id', $user->id)->where('status', 'En cours de traitement')->count(),
            'resolus'    => Incident::where('user_id', $user->id)->where('status', 'Résolu')->count(),
        ];

        return view('dashboard.citoyen', compact('incidents', 'stats'));
    }

    /**
     * Dashboard Technicien
     */
    public function technicien(): View
    {
        $user = auth()->user();

        // Incidents affectés à ce technicien
        $incidents = Incident::with(['category', 'user'])
            ->whereHas('affectations', function ($q) use ($user) {
                $q->where('technicien_id', $user->id);
            })
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total'      => Incident::whereHas('affectations', fn($q) => $q->where('technicien_id', $user->id))->count(),
            'en_cours'   => Incident::whereHas('affectations', fn($q) => $q->where('technicien_id', $user->id))
                                    ->where('status', 'En cours de traitement')->count(),
            'resolus'    => Incident::whereHas('affectations', fn($q) => $q->where('technicien_id', $user->id))
                                    ->where('status', 'Résolu')->count(),
            'en_attente' => Incident::whereHas('affectations', fn($q) => $q->where('technicien_id', $user->id))
                                    ->where('status', 'En attente')->count(),
        ];

        return view('dashboard.technicien', compact('incidents', 'stats'));
    }

    /**
     * Dashboard Administrateur
     */
    public function admin(): View
    {
        $totalIncidents = Incident::count();
        $resolus = Incident::where('status', 'Résolu')->count();

        $stats = [
            'total_incidents'   => $totalIncidents,
            'total_users'       => User::count(),
            'total_techniciens' => User::whereHas('roles', fn($q) => $q->where('name', 'technicien'))->count(),
            'taux_resolution'   => $totalIncidents > 0 ? round(($resolus / $totalIncidents) * 100) : 0,
        ];

        // Répartition par statut
        $byStatus = Incident::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        foreach (['En attente', 'En cours de traitement', 'Résolu', 'Rejeté'] as $s) {
            if (!isset($byStatus[$s])) {
                $byStatus[$s] = 0;
            }
        }

        // Répartition par catégorie (top 5)
        $byCategory = Incident::selectRaw('categories.name, count(*) as total')
            ->join('categories', 'incidents.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Incidents en attente d'affectation
        $pendingIncidents = Incident::with('category')
            ->where('status', 'En attente')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'byStatus',
            'byCategory',
            'pendingIncidents'
        ));
    }
}