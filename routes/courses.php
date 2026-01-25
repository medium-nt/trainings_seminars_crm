<?php

use App\Http\Controllers\CoursesController;
use Illuminate\Support\Facades\Route;

Route::prefix('/courses')->group(function () {
    Route::get('', [CoursesController::class, 'index'])
        ->can('is-admin-or-manager')
        ->name('courses.index');
    Route::get('/create', [CoursesController::class, 'create'])
        ->can('is-admin-or-manager')
        ->name('courses.create');
    Route::post('', [CoursesController::class, 'store'])
        ->can('is-admin-or-manager')
        ->name('courses.store');
    Route::get('/{course}/edit', [CoursesController::class, 'edit'])
        ->can('is-admin-or-manager')
        ->name('courses.edit');
    Route::put('/{course}', [CoursesController::class, 'update'])
        ->can('is-admin-or-manager')
        ->name('courses.update');
    Route::delete('/{course}', [CoursesController::class, 'destroy'])
        ->can('is-admin-or-manager')
        ->name('courses.destroy');
});
