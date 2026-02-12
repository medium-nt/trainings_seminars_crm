<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (app()->environment('local')) {
    Route::prefix('autologin')->group(function () {
        Route::get('/{email}', [UsersController::class, 'autologin'])
            ->name('users.autologin');
    });
}

Auth::routes(['verify' => true]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('home');

    Route::prefix('/profile')->group(function () {
        Route::get('', [App\Http\Controllers\UsersController::class, 'profile'])
            ->name('profile');
        Route::put('', [App\Http\Controllers\UsersController::class, 'profileUpdate'])
            ->name('profile.update');
        Route::delete('/company-card', [App\Http\Controllers\UsersController::class, 'deleteCompanyCard'])
            ->name('profile.company-card.delete');
    });

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::post('/', [App\Http\Controllers\DocumentsController::class, 'store'])->name('store');
        Route::delete('/{document}', [App\Http\Controllers\DocumentsController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [App\Http\Controllers\DocumentsController::class, 'download'])->name('download');
        Route::patch('/{document}/approve', [App\Http\Controllers\DocumentsController::class, 'approve'])->name('approve');
    });

    require base_path('routes/users.php');
    require base_path('routes/payments.php');
    require base_path('routes/courses.php');
    require base_path('routes/groups.php');
    require base_path('routes/schedules.php');
    require base_path('routes/statistics.php');
});
