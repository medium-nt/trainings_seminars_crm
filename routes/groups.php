<?php

use App\Http\Controllers\GroupsController;
use App\Models\Group;
use Illuminate\Support\Facades\Route;

Route::prefix('/groups')->group(function () {
    Route::get('', [GroupsController::class, 'index'])
        ->can('viewAny', Group::class)
        ->name('groups.index');
    Route::get('/create', [GroupsController::class, 'create'])
        ->can('create', Group::class)
        ->name('groups.create');
    Route::post('', [GroupsController::class, 'store'])
        ->can('create', Group::class)
        ->name('groups.store');
    Route::get('/{group}/edit', [GroupsController::class, 'edit'])
        ->can('update', 'group')
        ->name('groups.edit');
    Route::put('/{group}', [GroupsController::class, 'update'])
        ->can('update', 'group')
        ->name('groups.update');
    Route::delete('/{group}', [GroupsController::class, 'destroy'])
        ->can('delete', 'group')
        ->name('groups.destroy');
});
