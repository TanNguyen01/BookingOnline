<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\OpeningHour\OpeningHourController;
use App\Http\Controllers\Api\staff\StaffController;
use App\Http\Controllers\Api\StoreInformation\StoreInformationController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// user admin
Route::get('listuser', [UserController::class, 'index'])->name('list.users');
Route::get('list_user/{id}', [UserController::class, 'show'])->name('show.users');
Route::post('post', [UserController::class, 'store'])->name('store.user');
Route::post('user/{id}', [UserController::class, 'update'])->name('update.user');
Route::delete('deleteuser/{id}', [UserController::class, 'destroy'])->name('destroy.user');
// storeInformations
Route::get('liststore', [StoreInformationController::class, 'index'])->name('list.store');
Route::get('showstore/{id}', [StoreInformationController::class, 'show'])->name('show.store');
Route::post('storepost', [StoreInformationController::class, 'store'])->name('add.store');
Route::post('store/{id}', [StoreInformationController::class, 'update'])->name('update.store');
Route::delete('storedelete/{id}', [StoreInformationController::class, 'destroy'])->name('destroy.store');
//openginghour
Route::get('/opening', [OpeningHourController::class ,'index'])->name('list.opening');
Route::get('/opening/{storeName}', [OpeningHourController::class ,'show'])->name('show.opening');
Route::post('/opening_hours', [OpeningHourController::class, 'store'])->name('store.opening');
Route::post('store-hours/{storeName}',[OpeningHourController::class, 'update'])->name('opening_hours.update');
Route::delete('store-hours/delete/{storeName}',[OpeningHourController::class, 'destroy'])->name('opening_hours.destroy');


//staff
Route::middleware('auth:sanctum')->post('profile/update', [StaffController::class, 'updateProfile']);

// Route::post('profile/update',[StaffController::class , 'updateProfile']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})->name('auth');
// Route::get('/auth', function (Request $request) {

//     return response()->json(['message' => 'Vui lòng đăng nhập']);
// })->name('auth');





















Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
