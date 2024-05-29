<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleRequest;
use App\Http\Requests\StaffRequest;
use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Services\StaffService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Traits\APIResponse;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class StaffController extends Controller
{
    use APIResponse;

    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    public function updateProfile(StaffRequest $request)
    {
        $user = Auth::user();
        $validatedData = $request->all();
        $updatedUser = $this->staffService->updateProfile($user, $validatedData);

        if ($updatedUser === null) {
            return $this->responseUnAuthorized('Bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);
        }

        if ($updatedUser === false) {
            return $this->responseBadRequest('Mật khẩu hiện tại không chính xác', Response::HTTP_BAD_REQUEST);
        }

        return $this->responseSuccess('Cập nhật thành công', ['data' => $updatedUser]);
    }

    public function showProfile()
    {
        $user = Auth::user();
        $profile = $this->staffService->showProfileService($user);

        if ($profile === null) {
            return $this->responseUnAuthorized('Bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);
        }

        return $this->responseSuccess('Xem thành công', ['data' => $profile]);
    }

    public function createSchedule(ScheduleRequest $request)
    {
        $user = Auth::user();
        $storeId = $request->input('store_information_id');
        $schedules = $request->input('schedules');
        $createdSchedules = $this->staffService->createSchedule($user, $storeId, $schedules);

        if ($createdSchedules === null) {
            return $this->responseUnAuthorized('Bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);
        }

        if (isset($createdSchedules['error'])) {
            return response()->json(['error' => $createdSchedules['error']], Response::HTTP_BAD_REQUEST);
        }else

        return $this->responseCreated('Đăng ký giờ làm thành công', ['data' => $createdSchedules]);
    }

    public function getEmployeeBookings()
    {
        $user = Auth::user();
        $bookings = $this->staffService->getEmployeeBookings($user);

        if ($bookings === null) {
            return $this->responseUnAuthorized('Bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);
        }

        if ($bookings->isEmpty()) {
            return $this->responseNotFound('Hiện bạn không có booking nào', Response::HTTP_NOT_FOUND);
        }

        return $this->responseSuccess('Xem booking thành công', ['data' => $bookings]);
    }


    public function seeSchedule(){
       $schedules = $this->staffService->getSchedule();
       $response = $schedules->map(function($schedule) {
        $error = $schedule->is_valid == 0 ? 'Vui lòng kiểm tra lại giờ mở cửa của cửa hàng đã được thay đổi' : null;
        return [
            'id' => $schedule->id,
            'user_id' => $schedule->user_id,
            'store_information_id' => $schedule->store_information_id,
            'is_valid' => $schedule->is_valid,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'created_at' => $schedule->created_at,
            'error' => $error
        ];
    });
    return $this->responseSuccess('Xem lich lam thành công', ['data' => $response]);
    }

}
