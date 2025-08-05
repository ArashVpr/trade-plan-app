<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ChecklistWeightsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TradeJournalController;
use App\Http\Controllers\TradeManagementController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Guest routes (authentication)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Password reset routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Main application routes
    Route::get('/', [ChecklistController::class, 'checklistWeights'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Checklist weights
    Route::get('/checklist-weights', [ChecklistWeightsController::class, 'index'])->name('checklist-weights.index');
    Route::post('/checklist-weights', [ChecklistWeightsController::class, 'update'])->name('checklist-weights.update');

    // User profile
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [UserProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/notifications', [UserProfileController::class, 'updateNotifications'])->name('profile.notifications');
    Route::put('/profile/checklist-weights', [UserProfileController::class, 'updateChecklistWeights'])->name('profile.checklist-weights');
    Route::post('/profile', [UserProfileController::class, 'deleteAccount'])->name('profile.delete');

    // Checklists
    Route::get('/checklists', [ChecklistController::class, 'index'])->name('checklists.index');
    Route::post('/checklists', [ChecklistController::class, 'store'])->name('checklists.store');
    Route::get('/checklists/{checklist}', [ChecklistController::class, 'show'])->name('checklists.show');
    Route::get('/checklists/{checklist}/edit', [ChecklistController::class, 'edit'])->name('checklists.edit');
    Route::put('/checklists/{checklist}', [ChecklistController::class, 'update'])->name('checklists.update');
    Route::post('/checklists/{checklist}', [ChecklistController::class, 'destroy'])->name('checklists.destroy');

    // Trade Management
    Route::get('/trade-management', [TradeManagementController::class, 'index'])->name('trade-management.index');
    Route::post('/trade-management', [TradeManagementController::class, 'store'])->name('trade-management.store');
    Route::post('/trade-management/predefined', [TradeManagementController::class, 'addPredefined'])->name('trade-management.add-predefined');
    Route::put('/trade-management/{managementItem}/status', [TradeManagementController::class, 'updateStatus'])->name('trade-management.update-status');
    Route::put('/trade-management/{managementItem}', [TradeManagementController::class, 'update'])->name('trade-management.update');
    Route::delete('/trade-management/{managementItem}', [TradeManagementController::class, 'destroy'])->name('trade-management.destroy');
});

// Development/Test routes
Route::get('/test', function () {
    return Inertia::render('Test');
})->name('test');
