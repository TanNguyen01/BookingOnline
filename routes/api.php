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

Route::post('/set-locale/{locale}', function ($locale) {
    Session::put('locale', $locale);

    return response()->json(['message' => 'Locale set to '.$locale]);
});

Route::middleware(['auth:sanctum', 'checkadmin', 'language'])->group(function () {
    // Services
    Route::prefix('services')->group(function () {
        Route::get('/list', [ServiceController::class, 'index'])->name('list.service');
        Route::get('/{id}', [ServiceController::class, 'show'])->name('show.service');
        Route::post('/post', [ServiceController::class, 'store'])->name('store.service');
        Route::put('/update/{id}', [ServiceController::class, 'update'])->name('update.service');
        Route::delete('delete/{id}', [ServiceController::class, 'destroy'])->name('destroy.service');
    });

    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/list', [CategorieController::class, 'index'])->name('list.categorie');
        Route::get('/{id}', [CategorieController::class, 'show'])->name('show.categorie');
        Route::post('/post', [CategorieController::class, 'store'])->name('store.categorie');
        Route::put('/update/{id}', [CategorieController::class, 'update'])->name('update.categorie');
        Route::delete('/delete/{id}', [CategorieController::class, 'destroy'])->name('destroy.categorie');
    });

    // User Admin
    Route::prefix('admin_users')->group(function () {
        Route::get('/list', [UserController::class, 'index'])->name('list.users');
        Route::get('/show/{id}', [UserController::class, 'show'])->name('show.users');
        Route::post('/post', [UserController::class, 'store'])->name('store.user');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update.user');
        Route::delete('delete/{id}', [UserController::class, 'destroy'])->name('destroy.user');
    });

    // Store Informations
    Route::prefix('stores')->group(function () {
        Route::get('/list', [StoreInformationController::class, 'index']);
        Route::get('/show/{id}', [StoreInformationController::class, 'show'])->name('show.store');
        Route::post('/post', [StoreInformationController::class, 'store'])->name('add.store');
        Route::put('/update/{id}', [StoreInformationController::class, 'update'])->name('update.store');
        Route::delete('/delete/{id}', [StoreInformationController::class, 'destroy'])->name('destroy.store');
    });

    // Opening Hours
    Route::prefix('opening-hours')->group(function () {
        Route::get('/list', [OpeningHourController::class, 'index'])->name('list.opening');
        Route::get('/{storeid}', [OpeningHourController::class, 'show'])->name('show.opening');
        Route::post('/post/{storeId}', [OpeningHourController::class, 'store'])->name('store.opening');
        Route::post('/update/{storeId}', [OpeningHourController::class, 'update'])->name('opening_hours.update');
        Route::delete('delete/{id}', [OpeningHourController::class, 'destroy'])->name('opening_hours.destroy');
        // thêm 5 ngày mở cửa liên tiếp
        Route::post('/post_5day/{storeId}', [OpeningHourController::class, 'store5']);
    });
    // Booking Management
    Route::prefix('bookings')->group(function () {
        Route::get('/list', [BookingController::class, 'index']);
        Route::get('/{id}', [BookingController::class, 'show']);
        Route::post('/update/{id}', [BookingController::class, 'update']);
        Route::delete('/delete/{id}', [BookingController::class, 'destroy']);
    });
});
Route::prefix('client')->group(function () {
    Route::get('/list_time', [ClientController::class, 'chooseTime']);
    Route::get('/list-schedule', [ClientController::class, 'getWorkingHoursByUserAndStore']);
    Route::get('/list-user', [ClientController::class, 'getUsersByStoreInformation']);
    Route::get('/list-service', [ClientController::class, 'listService']);
    Route::get('/list-store', [ClientController::class, 'listStore']);
    Route::post('/store_booking', [BookingController::class, 'store']);
});
//nhân viên
Route::middleware(['auth:sanctum'])->group(function () {
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
