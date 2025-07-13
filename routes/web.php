<?php

use App\Http\Controllers\ChecklistController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('ChecklistWizard', 
        [
            'name' => 'Arash God'
        ]
    );
});

Route::middleware('auth')->group(function () {
    Route::get('/checklists', [ChecklistController::class, 'index'])->name('checklists.index');
    Route::post('/checklists', [ChecklistController::class, 'store'])->name('checklists.store');
});
