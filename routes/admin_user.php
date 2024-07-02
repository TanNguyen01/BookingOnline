<?php

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('admin_users', UserController::class)->only(['update', 'destroy'])->middleware('throttle:60,1');
Route::apiResource('admin_users', UserController::class)->except(['update', 'destroy']);
