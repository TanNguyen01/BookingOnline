<?php

use App\Http\Controllers\Api\Staff\StaffController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' =>'user'],function () {
    // xem lịch làm
    Route::get('/see-schedule', [StaffController::class, 'seeSchedule']);
    // thêm lịch làm user
    Route::post('/schedules', [StaffController::class, 'createSchedule']);
    // xem tất cả booking
    Route::get('/listbooking', [StaffController::class, 'getEmployeeBookings']);
    // xem giờ mở cửa của cửa hàng
    Route::get('/see-opeening-hours', [StaffController::class, 'viewStoreOpeningHours']);
});
