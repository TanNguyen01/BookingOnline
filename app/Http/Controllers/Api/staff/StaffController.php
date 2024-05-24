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
        return $this->staffService->updateProfile($user, $validatedData);
    }

    public function showProfile()
    {
        return $this->staffService->showProfileService();
    }

    public function CreateSchedule(ScheduleRequest $request)
    {

        $user = Auth::user();
        if ($user->role !== 1) {
            return $this->responseUnAuthorized('bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);
        }
        $storeId = $request->input('store_information_id');
        $schedules = $request->input('schedules');

        return $this->staffService->createSchedule($user, $storeId, $schedules);
    }

    public function getBookings()
    {

        return $this->staffService->getEmployeeBookings();
    }
}
