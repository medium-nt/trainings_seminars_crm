<?php

use App\Http\Controllers\ScheduleApiController;
use App\Http\Controllers\SchedulesController;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;

Route::prefix('/schedules')->group(function () {
    // Календарь
    Route::get('', [SchedulesController::class, 'index'])
        ->can('viewAny', Schedule::class)
        ->name('schedules.index');

    // CRUD
    Route::get('/create', [SchedulesController::class, 'create'])
        ->can('create', Schedule::class)
        ->name('schedules.create');
    Route::post('', [SchedulesController::class, 'store'])
        ->can('create', Schedule::class)
        ->name('schedules.store');
    Route::get('/{schedule}/edit', [SchedulesController::class, 'edit'])
        ->can('update', 'schedule')
        ->name('schedules.edit');
    Route::put('/{schedule}', [SchedulesController::class, 'update'])
        ->can('update', 'schedule')
        ->name('schedules.update');
    Route::delete('/{schedule}', [SchedulesController::class, 'destroy'])
        ->can('delete', 'schedule')
        ->name('schedules.destroy');

    // API для FullCalendar
    Route::get('/api/events', [ScheduleApiController::class, 'index'])
        ->name('schedules.api.events');
});
