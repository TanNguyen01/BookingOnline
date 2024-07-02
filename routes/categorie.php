<?php

use App\Http\Controllers\Api\Categorie\CategorieController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategorieController::class)->only(['update', 'destroy'])->middleware('throttle:60,1');
Route::apiResource('categories', CategorieController::class)->except(['update', 'destroy']);
