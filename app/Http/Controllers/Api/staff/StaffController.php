<?php

namespace App\Http\Controllers\Api\staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleRequest;
use App\Http\Requests\StaffRequest;
use App\Traits\APIResponse;
use App\Services\StaffService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        $validatedData = $request->validated();
        $staff = $this->staffService->updateProfile($user, $validatedData);
        return $this->responseSuccess(
            'Cập nhật thành công',
            [
                'data' => $staff,

            ],
            Response::HTTP_OK
        );
    }

    public function showProfile()
    {
        $userInfo = $this->staffService->showProfileService();

        if ($userInfo) {
            return $this->responseSuccess(
                'Xem thành công thành công',
                [
                    'data' => $userInfo,

                ],
                Response::HTTP_OK
            );
        } else {
            return $this->responseUnAuthorized(
                'bạn không có quyền truy cập',
                Response::HTTP_FORBIDDEN
            );
        }
    }

    public function CreateSchedule(ScheduleRequest $request)
    {
        $user = Auth::user();
        if ($user->role !== 1) {
            return $this->responseUnAuthorized('bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);
        }

        $storeId = $request->input('store_information_id');
        $schedules = $request->input('schedules');

        $result = $this->staffService->createSchedule($user, $storeId, $schedules);

        if (isset($result['error'])) {
            return response()->json($result, 401);
        }

        return $this->responseSuccess(
            'thêm thành công',
            [
                'data' => $result,

            ],
            Response::HTTP_OK
        );
    }

    public function getBookings()
    {

        $list =  $this->staffService->getEmployeeBookings();
        return $this->responseSuccess(
            'xem thành công',
            [
                'data' => $list,

            ],
            Response::HTTP_OK
        );
    }
}
