<?php

use App\Http\Controllers\Api\OpeningHour\OpeningHourController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' =>'opening-hours'],function () {
    // xem lịch làm
    Route::get('/list', [OpeningHourController::class, 'index']);
    Route::get('/{storeId}', [OpeningHourController::class, 'show']);
    Route::post('/post/{storeId}', [OpeningHourController::class, 'store']);
    Route::post('/update/{storeId}', [OpeningHourController::class, 'update'])->middleware('throttle:60,1');
    Route::delete('delete/{id}', [OpeningHourController::class, 'destroy'])->middleware('throttle:60,1');
    //xóa nhanh những ngày đã qua
    Route::delete('quick_delete/{storeId}', [OpeningHourController::class, 'quickDestroy']);
    // thêm 5 ngày mở cửa liên tiếp
    Route::post('/post_5day/{storeId}', [OpeningHourController::class, 'store5']);
});
