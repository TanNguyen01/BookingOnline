<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleRequest;
use App\Http\Requests\StaffRequest;
use App\Models\booking;
use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Services\StaffService;
use App\Traits\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

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
        $validatedData = $request->all();
        $user = $this->staffService->staffService();

        if (! Hash::check($validatedData['current_password'], $user->password)) {
            return $this->responseBadRequest([Response::HTTP_BAD_REQUEST, 'mật khẩu hiện tại không đúng']);
        }

        unset($validatedData['current_password']);

        if (isset($validatedData['new_password'])) {
            $validatedData['password'] = bcrypt($validatedData['new_password']);
            unset($validatedData['new_password']);
        }
        $this->staffService->uploadImageIfExists($validatedData, $user);
        $user->update($validatedData);

        return $this->responseSuccess('Cập nhật thành công', ['data' => $user]);
    }

    public function showProfile()
    {

        $profile = $this->staffService->staffService();

        return $this->responseSuccess('Xem thành công', [
            'id' => $profile->id,
            'email' => $profile->email,
            'name' => $profile->name,
            'image' => $profile->image,
            'address' => $profile->address,
            'phone' => $profile->phone,
            'created_at' => $profile->created_at,
        ]);

    }

    public function createSchedule(ScheduleRequest $request)
    {
        $user = $this->staffService->staffService();
        $schedules = $request->input('schedules');
        $storeId = $request->input('store_information_id');
        if (! $storeId) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, 'Không tìm thấy cửa hàng');
        } else {
            foreach ($schedules as $scheduleData) {
                $day = $scheduleData['day'];
                $startTime = Carbon::createFromFormat('H:i:s', $scheduleData['start_time']);
                $endTime = Carbon::createFromFormat('H:i:s', $scheduleData['end_time']);

                // Kiểm tra xem đã tồn tại lịch làm việc cho ngày này chưa
                $existingSchedule = Schedule::where('store_information_id', $storeId)
                    ->where('day', $day)
                    ->first();

                if ($existingSchedule) {

                    return $this->responseNotFound([Response::HTTP_NOT_FOUND, 'Ngày này đã đăng ký giờ làm', $day]);
                }

                $openingHours = OpeningHour::where('store_information_id', $storeId)
                    ->where('day', $day)
                    ->first();

                if (! $openingHours) {
                    return $this->responseNotFound([Response::HTTP_NOT_FOUND, 'Ngày này cửa hàng chưa cập nhật giờ mở ửa vui long đợi', $day]);
                }

                $storeOpeningTime = Carbon::createFromFormat('H:i:s', $openingHours->opening_time);
                $storeClosingTime = Carbon::createFromFormat('H:i:s', $openingHours->closing_time);

                if ($startTime->lt($storeOpeningTime) || $endTime->gt($storeClosingTime)) {
                    return $this->responseNotFound([Response::HTTP_NOT_FOUND, 'giờ bắt đầu phải nằm trong giờ mở cửa và đóng cửa']);
                }
                $schedule = new Schedule();
                $schedule->user_id = $user->id;
                $schedule->store_information_id = $storeId;
                $schedule->day = $day;
                $schedule->start_time = $startTime;
                $schedule->end_time = $endTime;
                $schedule->created_at = now();
                $schedule->save();
                $createdSchedules[] = $schedule;
            }

            return $this->responseCreated('Đăng ký giờ làm thành công', ['data' => $schedules]);
        }
    }

    public function getEmployeeBookings()
    {
        $user = $this->staffService->staffService();
        $bookings = Booking::where('user_id', $user->id)->get();

        if ($bookings->isEmpty()) {
            return $this->responseNotFound('Hiện bạn không có booking nào', Response::HTTP_NOT_FOUND);
        } else {
            return $this->responseSuccess('Xem booking thành công', ['data' => $bookings]);
        }
    }

    public function seeSchedule()
    {
        $user = $this->staffService->staffService();
        $schedules = Schedule::where('user_id', $user->id)->get();

        if ($schedules->isEmpty()) {
            return $this->responseNotFound('Không có lịch làm nào được tìm thấy', Response::HTTP_NOT_FOUND);
        }

        $response = $schedules->map(function ($schedule) {
            $error = $schedule->is_valid == 0 ? 'Vui lòng kiểm tra lại giờ mở cửa của cửa hàng đã được thay đổi' : null;

            return [
                'id' => $schedule->id,
                'user_id' => $schedule->user_id,
                'store_information_id' => $schedule->store_information_id,
                'is_valid' => $schedule->is_valid,
                'day' => $schedule->day,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'created_at' => $schedule->created_at,
                'error' => $error,
            ];
        });

        return $this->responseSuccess('Xem lich lam thành công', ['data' => $response]);
    }
}
