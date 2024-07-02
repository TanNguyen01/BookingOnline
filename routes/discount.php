<?php

use App\Http\Controllers\Api\Promotion\PromotionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('discount', PromotionController::class);
