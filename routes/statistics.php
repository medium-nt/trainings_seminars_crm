<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/check-documents-approval', [App\Http\Controllers\StatisticsController::class, 'documents'])->name('check-documents-approval');
    });
});
