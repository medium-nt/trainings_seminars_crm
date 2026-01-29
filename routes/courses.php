<?php

use App\Http\Controllers\CoursesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['blocked'])->prefix('/courses')->group(function () {
    Route::get('', [CoursesController::class, 'index'])
        ->can('is-admin')
        ->name('courses.index');
    Route::get('/create', [CoursesController::class, 'create'])
        ->can('is-admin')
        ->name('courses.create');
    Route::post('', [CoursesController::class, 'store'])
        ->can('is-admin')
        ->name('courses.store');
    Route::get('/{course}/edit', [CoursesController::class, 'edit'])
        ->can('is-admin')
        ->name('courses.edit');
    Route::put('/{course}', [CoursesController::class, 'update'])
        ->can('is-admin')
        ->name('courses.update');
    Route::delete('/{course}', [CoursesController::class, 'destroy'])
        ->can('is-admin')
        ->name('courses.destroy');
});
