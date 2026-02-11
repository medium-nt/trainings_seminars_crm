<?php

use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/check-documents-approval', [StatisticsController::class, 'documents'])
            ->middleware('can:is-admin-or-manager')
            ->name('check-documents-approval');
        Route::get('/check-payments', [StatisticsController::class, 'payments'])
            ->middleware('can:is-admin-or-manager')
            ->name('check-payments');
    });
});
