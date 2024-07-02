<?php

use App\Http\Controllers\Api\StoreInformation\StoreInformationController;
use Illuminate\Support\Facades\Route;
Route::apiResource('stores', StoreInformationController::class)->only(['update', 'destroy'])->middleware('throttle:60,1');
    Route::apiResource('stores', StoreInformationController::class)->except(['update', 'destroy']);
