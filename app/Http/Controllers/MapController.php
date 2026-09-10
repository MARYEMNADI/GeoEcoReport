<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(): View
    {
        $incidents = Incident::with(['category', 'user'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get();

        return view('map.index', compact('incidents'));
    }
}