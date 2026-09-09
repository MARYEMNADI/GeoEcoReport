<?php

use App\Http\Controllers\AffectationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentStatusController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Route principale
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

// Afficher Login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

// Traiter Login
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login.submit');

// Afficher Register
Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->middleware('guest')
    ->name('register');

// Traiter Register
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register.submit');


/*
|--------------------------------------------------------------------------
| Routes protégées par authentification
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    /*
    |--------------------------------------------------------------------------
    | Dashboard principal
    |--------------------------------------------------------------------------
    */

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

        abort(
            403,
            'Aucun rôle attribué à cet utilisateur.'
        );

    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Dashboards
    |--------------------------------------------------------------------------
    */

    // Dashboard Citoyen
    Route::get(
        '/citoyen/dashboard',
        [DashboardController::class, 'citoyen']
    )
        ->middleware('role:citoyen')
        ->name('citoyen.dashboard');


    // Dashboard Technicien
    Route::get(
        '/technicien/dashboard',
        [DashboardController::class, 'technicien']
    )
        ->middleware('role:technicien')
        ->name('technicien.dashboard');


    // Dashboard Administrateur
    Route::get(
        '/admin/dashboard',
        [DashboardController::class, 'admin']
    )
        ->middleware('role:administrateur')
        ->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Incidents CRUD
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'incidents',
        IncidentController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Suppression d'une image
    |--------------------------------------------------------------------------
    */

    Route::delete(
        '/incidents/{incident}/images/{image}',
        [IncidentController::class, 'destroyImage']
    )->name('incidents.images.destroy');


    /*
    |--------------------------------------------------------------------------
    | Affectation d'un incident à un technicien
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/incidents/{incident}/affectations',
        [AffectationController::class, 'store']
    )->name('incidents.assign');


    /*
    |--------------------------------------------------------------------------
    | Changement du statut
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/incidents/{incident}/status',
        [IncidentStatusController::class, 'updateStatus']
    )->name('incidents.status.update');


    /*
    |--------------------------------------------------------------------------
    | Commentaires
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/incidents/{incident}/comments',
        [CommentController::class, 'store']
    )->name('comments.store');


    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    // Marquer toutes les notifications comme lues
    Route::patch(
        '/notifications/read-all',
        [NotificationController::class, 'markAllAsRead']
    )->name('notifications.read-all');


    // Marquer une notification comme lue
    Route::patch(
        '/notifications/{id}/read',
        [NotificationController::class, 'markAsRead']
    )->name('notifications.read');


    /*
    |--------------------------------------------------------------------------
    | Catégories
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'categories',
        CategoryController::class
    )->except([
        'show'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Assistant GeoEco
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/assistant',
        [AssistantController::class, 'index']
    )->name('assistant.index');


    Route::post(
        '/assistant/ask',
        [AssistantController::class, 'ask']
    )->name('assistant.ask');


    Route::post(
        '/assistant/clear',
        [AssistantController::class, 'clear']
    )->name('assistant.clear');


    /*
    |--------------------------------------------------------------------------
    | Carte des incidents
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/map',
        [MapController::class, 'index']
    )->name('map.index');

});