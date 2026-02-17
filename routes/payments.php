<?php

use App\Http\Controllers\PaymentsController;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'blocked'])->prefix('payments')->group(function () {
    Route::get('', [PaymentsController::class, 'index'])
        ->can('viewAny', Payment::class)
        ->name('payments.index');

    Route::get('/create', [PaymentsController::class, 'create'])
        ->can('create', Payment::class)
        ->name('payments.create');

    Route::post('', [PaymentsController::class, 'store'])
        ->can('create', Payment::class)
        ->name('payments.store');

    Route::get('/{payment}/download', [PaymentsController::class, 'downloadReceipt'])
        ->name('payments.download');

    Route::get('/{payment}/edit', [PaymentsController::class, 'edit'])
        ->can('update', Payment::class)
        ->name('payments.edit');

    Route::put('/{payment}', [PaymentsController::class, 'update'])
        ->can('update', Payment::class)
        ->name('payments.update');

    Route::delete('/{payment}', [PaymentsController::class, 'destroy'])
        ->can('delete', Payment::class)
        ->name('payments.destroy');
});
