<?php

use App\Http\Controllers\Api\Service\ServiceController;
use Illuminate\Support\Facades\Route;
Route::apiResource('services', ServiceController::class)->only(['update', 'destroy'])->middleware('throttle:60,1');
Route::apiResource('services', ServiceController::class)->except(['update', 'destroy']);
