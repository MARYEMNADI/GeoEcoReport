<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentStatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================================
// 1. الصفحة الرئيسية
// ============================================================

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');


// ============================================================
// 2. Routes الخاصة بالزوار
// ============================================================

Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');

    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.submit');
});


// ============================================================
// 3. Logout
// ============================================================

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


// ============================================================
// 4. Routes الخاصة بالمستخدمين المسجلين
// ============================================================

Route::middleware('auth')->group(function () {

    // --------------------------------------------------------
    // Dashboards حسب الدور
    // --------------------------------------------------------

    Route::get('/citoyen/dashboard', [DashboardController::class, 'citoyen'])
        ->middleware('role:citoyen')
        ->name('citoyen.dashboard');


    Route::get('/technicien/dashboard', [DashboardController::class, 'technicien'])
        ->middleware('role:technicien')
        ->name('technicien.dashboard');


    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:administrateur')
        ->name('admin.dashboard');


    // --------------------------------------------------------
    // Gestion des incidents - CRUD
    // --------------------------------------------------------

    Route::resource('incidents', IncidentController::class);


    // --------------------------------------------------------
    // Changement du statut d'un incident
    // --------------------------------------------------------

    Route::patch(
        '/incidents/{incident}/status',
        [IncidentStatusController::class, 'updateStatus']
    )->name('incidents.status.update');


    // --------------------------------------------------------
    // Gestion des commentaires
    // --------------------------------------------------------

    Route::post(
        '/incidents/{incident}/comments',
        [CommentController::class, 'store']
    )->name('comments.store');

});