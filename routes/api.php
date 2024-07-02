<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/set-locale/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return response()->json(['message' => 'Locale set to ' . $locale]);
});

Route::middleware(['auth:sanctum', 'checkadmin', 'language','throttle'])->group(function () {
require_once __DIR__ . '/admin_user.php';
require_once __DIR__ . '/booking.php';
require_once __DIR__ . '/categorie.php';
require_once __DIR__ . '/discount.php';
require_once __DIR__ . '/opening_hour.php';
require_once __DIR__ . '/service.php';
require_once __DIR__ . '/statistic.php';
require_once __DIR__ . '/store.php';
});

Route::middleware(['language'])->group(function () {
    require_once __DIR__ . '/client.php';
    require_once __DIR__ . '/auth.php';


});
Route::middleware(['auth:sanctum', 'language'])->group(function () {
    require_once __DIR__ . '/profile.php';
});

Route::middleware('auth:sanctum', 'checkuser')->group(function () {
    // xem lịch làm
    require_once __DIR__ . '/user.php';
});


Route::get('test', [\App\Http\Controllers\TestController::class, 'test']);
// Route::get('test2', [StaffController::class, 'getMail']);
