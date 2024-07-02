<?php

use App\Http\Controllers\Api\Booking\BookingController;
use Illuminate\Support\Facades\Route;

Route::apiResource('bookings', BookingController::class)->only(['update', 'destroy'])->middleware('throttle:60,1');
Route::apiResource('bookings', BookingController::class)->except(['update', 'destroy']);
