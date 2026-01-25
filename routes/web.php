<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('home');

    Route::prefix('/profile')->group(function () {
        Route::get('', [App\Http\Controllers\UsersController::class, 'profile'])
            ->name('profile');
        Route::put('', [App\Http\Controllers\UsersController::class, 'profileUpdate'])
            ->name('profile.update');
    });

    require base_path('routes/users.php');
    require base_path('routes/courses.php');
    require base_path('routes/groups.php');
});
