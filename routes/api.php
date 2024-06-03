<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Categorie\CategorieController;
use App\Http\Controllers\Api\OpeningHour\OpeningHourController;
use App\Http\Controllers\Api\Service\ServiceController;
use App\Http\Controllers\Api\staff\StaffController;
use App\Http\Controllers\Api\StoreInformation\StoreInformationController;
use App\Http\Controllers\Api\User\UserController;
use App\Models\booking;
use Illuminate\Http\Request;
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

Route::get('/set-locale/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return response()->json(['message' => 'Locale set to ' . $locale]);
});

Route::middleware(['auth:sanctum', 'checkadmin'])->group(function () {
    // Services
    Route::get('list_service', [ServiceController::class, 'index'])->name('list.service');
    Route::get('service/{id}', [ServiceController::class, 'show'])->name('show.service');
    Route::post('service_post', [ServiceController::class, 'store'])->name('store.service');
    Route::post('service_update/{id}', [ServiceController::class, 'update'])->name('update.service');
    Route::delete('delete_service/{id}', [ServiceController::class, 'destroy'])->name('destroy.service');

    // Categories
    Route::get('list_categorie', [CategorieController::class, 'index'])->name('list.categorie');
    Route::get('categorie/{id}', [CategorieController::class, 'show'])->name('show.categorie');
    Route::post('categorie_post', [CategorieController::class, 'store'])->name('store.categorie');
    Route::post('categorie_update/{id}', [CategorieController::class, 'update'])->name('update.categorie');
    Route::delete('delete_categorie/{id}', [CategorieController::class, 'destroy'])->name('destroy.categorie');

    // User Admin
    Route::get('list_user', [UserController::class, 'index'])->name('list.users');
    Route::get('show_user/{id}', [UserController::class, 'show'])->name('show.users');
    Route::post('post', [UserController::class, 'store'])->name('store.user');
    Route::put('user/{id}', [UserController::class, 'update'])->name('update.user');
    Route::delete('deleteuser/{id}', [UserController::class, 'destroy'])->name('destroy.user');

    // Store Informations
    Route::get('list_store', [StoreInformationController::class, 'index'])->name('list.store');
    Route::get('shows_store/{id}', [StoreInformationController::class, 'show'])->name('show.store');
    Route::post('store_post', [StoreInformationController::class, 'store'])->name('add.store');
    Route::post('store/{id}', [StoreInformationController::class, 'update'])->name('update.store');
    Route::delete('store_delete/{id}', [StoreInformationController::class, 'destroy'])->name('destroy.store');

    // Opening Hours
    Route::get('/opening', [OpeningHourController::class, 'index'])->name('list.opening');
    Route::get('/opening/{storeid}', [OpeningHourController::class, 'show'])->name('show.opening');
    Route::post('/opening_hours', [OpeningHourController::class, 'store'])->name('store.opening');
    Route::post('update_hours', [OpeningHourController::class, 'update'])->name('opening_hours.update');
    Route::delete('store_hours/delete/{id}', [OpeningHourController::class, 'destroy'])->name('opening_hours.destroy');


    //quản lý booking
    Route::get('/listbooking', [BookingController::class, 'index']);
    Route::get('/booking/{id}', [BookingController::class, 'show']);
    Route::post('/update_booking/{id}', [BookingController::class, 'update']);
    Route::delete('/delete_booking/{id}', [BookingController::class, 'destroy']);
});

//
//tạo booking khách hàng
// chọn user
Route::post('/choose-employee', [BookingController::class, 'chooseEmployee']);
// chọn dịch vụ
Route::post('/choose-service', [BookingController::class, 'chooseService']);
// chọn ngày giờ
Route::post('/choose-date', [BookingController::class, 'chooseDate']);
// submit form
Route::post('/bookings', [BookingController::class, 'store']);



//nhân viên

Route::middleware('auth:sanctum', 'checkuser')->group(function () {
    // xem lịch làm
    Route::get('seeSchedule', [StaffController::class, 'seeSchedule']);
    // thêm lịch làm user
    Route::post('schedules', [StaffController::class, 'CreateSchedule']);
    //  update profile user
    Route::post('profile/update', [StaffController::class, 'updateProfile']);
    // xem profile user
    Route::get('showprofile', [StaffController::class, 'showProfile']);

    // xem tất cả booking
    Route::get('listbooking', [StaffController::class, 'getEmployeeBookings']);
});
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('login', [AuthController::class, 'login'])->name('login');











// Route::get('/auth', function (Request $request) {
//     return response()->json(['message' => 'Vui lòng đăng nhập']);
// })->name('auth');


Route::get('test', [\App\Http\Controllers\TestController::class, 'test']);
