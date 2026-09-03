<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentStatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - GeoEcoReport
|--------------------------------------------------------------------------
*/


// ============================================================
// 1. الصفحة الرئيسية
// ============================================================

Route::get('/', function () {

    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');

})->name('home');


// ============================================================
// 2. Routes الخاصة بالزوار
// ============================================================

Route::middleware('guest')->group(function () {

    // -------------------------
    // Login
    // -------------------------

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');


    // -------------------------
    // Register
    // -------------------------

    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.submit');
});


// ============================================================
// 3. Logout
// ============================================================

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// ============================================================
// 4. Routes الخاصة بالمستخدمين المسجلين
// ============================================================

Route::middleware('auth')->group(function () {

    // ========================================================
    // Dashboard principal
    // ========================================================

    Route::get('/dashboard', function () {

        $user = auth()->user();

        // Administrateur
        if ($user->hasRole('administrateur')) {
            return redirect()->route('admin.dashboard');
        }

        // Technicien
        if ($user->hasRole('technicien')) {
            return redirect()->route('technicien.dashboard');
        }

        // Citoyen
        if ($user->hasRole('citoyen')) {
            return redirect()->route('citoyen.dashboard');
        }

        abort(403, 'Aucun rôle attribué à cet utilisateur.');

    })->name('dashboard');


    // ========================================================
    // Dashboards حسب الدور
    // ========================================================

    Route::get('/citoyen/dashboard', [DashboardController::class, 'citoyen'])
        ->middleware('role:citoyen')
        ->name('citoyen.dashboard');


    Route::get('/technicien/dashboard', [DashboardController::class, 'technicien'])
        ->middleware('role:technicien')
        ->name('technicien.dashboard');


    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:administrateur')
        ->name('admin.dashboard');


    // ========================================================
    // Gestion des incidents - CRUD
    // ========================================================

    Route::resource('incidents', IncidentController::class);


    // ========================================================
    // Changement du statut
    // ========================================================

    Route::patch(
        '/incidents/{incident}/status',
        [IncidentStatusController::class, 'updateStatus']
    )->name('incidents.status.update');


    // ========================================================
    // Commentaires
    // ========================================================

    Route::post(
        '/incidents/{incident}/comments',
        [CommentController::class, 'store']
    )->name('comments.store');

});