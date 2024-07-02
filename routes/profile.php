<?php

use App\Http\Controllers\Api\Staff\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/showprofile', [StaffController::class, 'showProfile']);
Route::post('/profile/update', [StaffController::class, 'updateProfile']);

