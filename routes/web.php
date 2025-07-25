<?php

use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\TradeJournalController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render(
//         'ChecklistWizard',
//         [
//             'name' => 'Arash God'
//         ]
//     );
// });

Route::get('/', [ChecklistController::class, 'checklistWeights'])->name('home');

Route::middleware('auth')->group(function () {});

    Route::get('/user-settings', [UserSettingsController::class, 'index'])->name('user-settings.index');
    Route::post('/user-settings', [UserSettingsController::class, 'update'])->name('user-settings.update');

    Route::get('/checklists', [ChecklistController::class, 'index'])->name('checklists.index');
    Route::post('/checklists', [ChecklistController::class, 'store'])->name('checklists.store');
    Route::get('/checklists/{checklist}', [ChecklistController::class, 'show'])->name('checklists.show');
    Route::get('/checklists/{checklist}/edit', [ChecklistController::class, 'edit'])->name('checklists.edit');
    Route::put('/checklists/{checklist}', [ChecklistController::class, 'update'])->name('checklists.update');
    Route::post('/checklists/{checklist}', [ChecklistController::class, 'destroy'])->name('checklists.destroy');

    Route::get('/test', function () {
        return Inertia::render('Test');
    })->name('test');


// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');
