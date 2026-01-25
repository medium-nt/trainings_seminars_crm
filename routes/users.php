<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('/users')->group(function () {
    Route::get('', [UsersController::class, 'index'])
        ->name('users.index');
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
});
