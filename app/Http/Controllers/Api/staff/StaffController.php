<?php

namespace App\Http\Controllers\Api\staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleRequest;
use App\Http\Requests\StaffRequest;
use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Services\StaffService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
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
        return response()->json(['message' => 'Hồ sơ đã được cập nhật thành công']);
    }

    public function showProfile()
    {
        $userInfo = $this->staffService->showProfileService();

        if ($userInfo) {
            return response()->json($userInfo, 201);
        } else {
            return response()->json(['message' => 'Lỗi k tìm thấy'], 401);
        }
    }

    public function CreateSchedule(ScheduleRequest $request)
    {
        $user = Auth::user();
        if ($user->role !== 1) {
            return response()->json(['error' => 'Bạn không có quyền cập nhật hồ sơ'], 401);
        }

        $storeId = $request->input('store_information_id');
        $schedules = $request->input('schedules');

        $result = $this->staffService->createSchedule($user, $storeId, $schedules);

        if (isset($result['error'])) {
            return response()->json($result, 400);
        }

        return response()->json($result, 200);
    }
}
