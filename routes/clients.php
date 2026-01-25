<?php

use App\Http\Controllers\ClientsController;
use Illuminate\Support\Facades\Route;

Route::get('/search', [ClientsController::class, 'search'])
    ->name('clients.search');
