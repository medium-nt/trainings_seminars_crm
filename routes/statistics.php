<?php

use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/check-documents-approval', [StatisticsController::class, 'documents'])
            ->name('check-documents-approval');
        Route::get('/check-payments', [StatisticsController::class, 'payments'])
            ->name('check-payments');
    });
});
