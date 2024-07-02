<?php

use App\Http\Controllers\Api\Statistic\StatisticsController;
use Illuminate\Support\Facades\Route;
Route::group(['prefix' =>'statistics'],function () {

Route::get('/total-bookings', [StatisticsController::class, 'getTotalBookings']);
// tỷ lệ lấp đầy thời gian
Route::get('/occupancy-rate', [StatisticsController::class, 'getOccupancyRate']);
// Số lần đặt chỗ của các user
Route::get('/user-bookings', [StatisticsController::class, 'getUserBookings']);
// Giá trị trung bình đơn hàng
Route::get('/average-booking-value', [StatisticsController::class, 'getAverageBookingValue']);
// Tỷ lệ từ bỏ đặt chỗ
Route::get('/abandonment-rate', [StatisticsController::class, 'getAbandonmentRate']);
//Tổng doanh thu từ các lượt đặt chỗ.
Route::get('/gettotal-revenue', [StatisticsController::class, 'getTotalRevenue']);
// doanh thu theo dịch vụ
Route::get('/gettotal-service', [StatisticsController::class, 'getServiceRevenueByStore']);
});
