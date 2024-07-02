<?php

use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Client\ClientController;
use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'client'],function () {
Route::get('/list_time', [ClientController::class, 'chooseTime']);
Route::get('/get-date-working-of-user', [ClientController::class, 'GetDateWorkingOfUser']);
Route::get('/list-schedule', [ClientController::class, 'getWorkingHoursByUserAndStore']);
Route::get('/list-user', [ClientController::class, 'getUsersByStoreInformation']);
Route::get('/list-service', [ClientController::class, 'listService']);
Route::get('/list-store', [ClientController::class, 'listStore']);
Route::post('/store_booking', [BookingController::class, 'store']);
});
