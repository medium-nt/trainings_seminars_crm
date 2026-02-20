<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['blocked'])->prefix('/users')->group(function () {
    Route::get('/clients', [UsersController::class, 'clients'])
        ->name('users.clients');
    Route::get('/employees', [UsersController::class, 'employees'])
        ->can('is-admin')
        ->name('users.employees');
    Route::get('/create', [UsersController::class, 'create'])
        ->name('users.create');
    Route::post('/create', [UsersController::class, 'store'])
        ->name('users.store');
    Route::get('/{user}/edit', [UsersController::class, 'edit'])
        ->name('users.edit');
    Route::put('/{user}', [UsersController::class, 'update'])
        ->name('users.update');
    Route::delete('/{user}', [UsersController::class, 'destroy'])
        ->name('users.destroy');
    Route::post('/{user}/toggle-block', [UsersController::class, 'toggleBlock'])
        ->name('users.toggleBlock');

    Route::post('/{user}/company-card', [UsersController::class, 'uploadCompanyCardForUser'])
        ->name('users.company-card.upload');
    Route::delete('/{user}/company-card', [UsersController::class, 'deleteCompanyCardForUser'])
        ->name('users.company-card.delete');

    Route::get('/search', [UsersController::class, 'search'])
        ->name('clients.search');
});
