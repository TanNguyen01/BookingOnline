<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Categorie\CategorieController;
use App\Http\Controllers\Api\Client\ClientController;
use App\Http\Controllers\Api\OpeningHour\OpeningHourController;
use App\Http\Controllers\Api\Service\ServiceController;
use App\Http\Controllers\Api\Staff\StaffController;
use App\Http\Controllers\Api\StoreInformation\StoreInformationController;
use App\Http\Controllers\Api\User\UserController;
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
Route::put('/update2/{id}', [StoreInformationController::class, 'update2']);

Route::post('/set-locale/{locale}', function ($locale) {
    Session::put('locale', $locale);

    return response()->json(['message' => 'Locale set to '.$locale]);
});

Route::middleware(['auth:sanctum', 'checkadmin', 'language'])->group(function () {
    // Services
    Route::apiResource('services', ServiceController::class)->only(['update', 'destroy'])->middleware('rate.limit');
    Route::apiResource('services', ServiceController::class)->except(['update', 'destroy']);

    // Categories
    Route::apiResource('categories', CategorieController::class)->only(['update', 'destroy'])->middleware('rate.limit');
    Route::apiResource('categories', CategorieController::class)->except(['update', 'destroy']);

    // User Admin
    Route::apiResource('admin_users', UserController::class)->only(['update', 'destroy'])->middleware('rate.limit');
    Route::apiResource('admin_users', UserController::class)->except(['update', 'destroy']);

    // Store Informations
    Route::apiResource('stores', StoreInformationController::class)->only(['update', 'destroy'])->middleware('rate.limit');
    Route::apiResource('stores', StoreInformationController::class)->except(['update', 'destroy']);

    // Opening Hours
    Route::prefix('opening-hours')->group(function () {
        Route::get('/list', [OpeningHourController::class, 'index']);
        Route::get('/{storeId}', [OpeningHourController::class, 'show']);
        Route::post('/post/{storeId}', [OpeningHourController::class, 'store']);
        Route::post('/update/{storeId}', [OpeningHourController::class, 'update'])->middleware('rate.limit');
        Route::delete('delete/{id}', [OpeningHourController::class, 'destroy'])->middleware('rate.limit');
        //xóa nhanh những ngày đã qua
        Route::delete('quick_delete/{storeId}', [OpeningHourController::class, 'quickDestroy']);
        // thêm 5 ngày mở cửa liên tiếp
        Route::post('/post_5day/{storeId}', [OpeningHourController::class, 'store5']);
    });
    // Booking Management
    Route::apiResource('bookings', BookingController::class)->only(['update', 'destroy'])->middleware('rate.limit');
    Route::apiResource('bookings', BookingController::class)->except(['update', 'destroy']);
});

Route::prefix('client')->middleware(['throttle', 'language'])->group(function () {
    Route::get('/list_time', [ClientController::class, 'chooseTime']);
    Route::get('/get-date-working-of-user', [ClientController::class, 'GetDateWorkingOfUser']);
    Route::get('/list-schedule', [ClientController::class, 'getWorkingHoursByUserAndStore']);
    Route::get('/list-user', [ClientController::class, 'getUsersByStoreInformation']);
    Route::get('/list-service', [ClientController::class, 'listService']);
    Route::get('/list-store', [ClientController::class, 'listStore']);
    Route::post('/store_booking', [BookingController::class, 'store']);
});
//nhân viên
Route::middleware([ 'auth:sanctum','language'])->group(function () {
    Route::get('/showprofile', [StaffController::class, 'showProfile']);
    Route::post('/profile/update', [StaffController::class, 'updateProfile']);
});

Route::middleware('auth:sanctum', 'checkuser')->prefix('user')->group(function () {
    // xem lịch làm
    Route::get('/see-schedule', [StaffController::class, 'seeSchedule']);
    // thêm lịch làm user
    Route::post('/schedules', [StaffController::class, 'createSchedule']);
    // xem tất cả booking
    Route::get('/listbooking', [StaffController::class, 'getEmployeeBookings']);
    // xem giờ mở cửa của cửa hàng
    Route::get('/see-opeening-hours', [StaffController::class, 'viewStoreOpeningHours']);
});

Route::middleware('language')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
Route::get('test', [\App\Http\Controllers\TestController::class, 'test']);
Route::get('/list-staff', [StoreInformationController::class, 'listStaff']);
Route::put('/update2/{id}', [StoreInformationController::class, 'update2']);


