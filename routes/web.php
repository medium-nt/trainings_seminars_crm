<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::prefix('/profile')->group(function () {
        Route::get('', [App\Http\Controllers\UsersController::class, 'profile'])
            ->name('profile');
        Route::put('', [App\Http\Controllers\UsersController::class, 'profileUpdate'])
            ->name('profile.update');
    });

    Route::prefix('/users')->group(function () {
        Route::get('', [App\Http\Controllers\UsersController::class, 'index'])
            ->name('users.index');
        Route::get('/create', [App\Http\Controllers\UsersController::class, 'create'])
            ->name('users.create');
        Route::post('/create', [App\Http\Controllers\UsersController::class, 'store'])
            ->name('users.store');
        Route::get('/{user}/edit', [App\Http\Controllers\UsersController::class, 'edit'])
            ->name('users.edit');
        Route::put('/{user}', [App\Http\Controllers\UsersController::class, 'update'])
            ->name('users.update');
        Route::delete('/{user}', [App\Http\Controllers\UsersController::class, 'destroy'])
            ->name('users.destroy');
    });
});
