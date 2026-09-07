<?php

use App\Http\Controllers\AffectationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentStatusController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    // Dashboard principal
    Route::get('/dashboard', function () {

        $user = auth()->user();

        if ($user->hasRole('administrateur')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('technicien')) {
            return redirect()->route('technicien.dashboard');
        }

        if ($user->hasRole('citoyen')) {
            return redirect()->route('citoyen.dashboard');
        }

        abort(403, 'Aucun rôle attribué à cet utilisateur.');

    })->name('dashboard');


    // Dashboards
    Route::get('/citoyen/dashboard', [DashboardController::class, 'citoyen'])
        ->middleware('role:citoyen')
        ->name('citoyen.dashboard');

    Route::get('/technicien/dashboard', [DashboardController::class, 'technicien'])
        ->middleware('role:technicien')
        ->name('technicien.dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:administrateur')
        ->name('admin.dashboard');


    // Incidents CRUD
    Route::resource('incidents', IncidentController::class);


    // Affectation
    Route::post(
        '/incidents/{incident}/affectations',
        [AffectationController::class, 'store']
    )->name('incidents.assign');


    // Changement du statut
    Route::patch(
        '/incidents/{incident}/status',
        [IncidentStatusController::class, 'update']
    )->name('incidents.status.update');


    // Commentaires
    Route::post(
        '/incidents/{incident}/comments',
        [CommentController::class, 'store']
    )->name('comments.store');

});