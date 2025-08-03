<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TradeJournalController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Guest routes (authentication)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Main application routes
    Route::get('/', [ChecklistController::class, 'checklistWeights'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User settings
    Route::get('/user-settings', [UserSettingsController::class, 'index'])->name('user-settings.index');
    Route::post('/user-settings', [UserSettingsController::class, 'update'])->name('user-settings.update');

    // Checklists
    Route::get('/checklists', [ChecklistController::class, 'index'])->name('checklists.index');
    Route::post('/checklists', [ChecklistController::class, 'store'])->name('checklists.store');
    Route::get('/checklists/{checklist}', [ChecklistController::class, 'show'])->name('checklists.show');
    Route::get('/checklists/{checklist}/edit', [ChecklistController::class, 'edit'])->name('checklists.edit');
    Route::put('/checklists/{checklist}', [ChecklistController::class, 'update'])->name('checklists.update');
    Route::post('/checklists/{checklist}', [ChecklistController::class, 'destroy'])->name('checklists.destroy');
});

// Development/Test routes
Route::get('/test', function () {
    return Inertia::render('Test');
})->name('test');
