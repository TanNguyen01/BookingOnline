<?php

namespace App\Services;

use App\Models\booking;
use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\APIResponse;


class StaffService
{
    use APIResponse;
    public function updateProfile($user, $validatedData)
    {
        // Kiểm tra xem người dùng có quyền là nhân viên không
        if ($user->role !== 1) {
            return $this->responseUnAuthorized('bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);

        }

        // Kiểm tra xem mật khẩu hiện tại có chính xác không
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return $this->responseBadRequest(
                'Mật khẩu hiện tại không chính xác',
                Response::HTTP_BAD_REQUEST
            );
        }

        // Loại bỏ trường mật khẩu hiện tại để tránh lưu vào cơ sở dữ liệu
        unset($validatedData['current_password']);

        // Nếu có mật khẩu mới, mã hóa và cập nhật vào cơ sở dữ liệu
        if (isset($validatedData['new_password'])) {
            $validatedData['password'] = bcrypt($validatedData['new_password']);
            unset($validatedData['new_password']);
        }

        // Cập nhật thông tin hồ sơ của người dùng

        $this->uploadImageIfExists($validatedData);
        $user->update($validatedData);
        return $this->responseSuccess(
            'Cập nhật thành công',
            [
                'data' => $user,

            ],
        );
    }


    public function showProfileService()
    {
        $user = Auth::user();

        if ($user->role == 1) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'image' => $user->image,
                'address' => $user->address,
                'phone' => $user->phone,
                'created_at' => $user->created_at
            ];
            return $this->responseSuccess(
                'Xem thành công thành công',
                [
                    'data' => $user,

                ],
            );
        } else {
            return $this->responseUnAuthorized(
                'bạn không có quyền truy cập',
                Response::HTTP_FORBIDDEN
            );
        }
    }

    public function createSchedule($user, $storeId, $schedules)
    {

        $user = Auth::user();
        if ($user->role !== 1) {
            return $this->responseUnAuthorized('bạn không có quyền truy cập', Response::HTTP_FORBIDDEN);
        }
        foreach ($schedules as $scheduleData) {
            $day = $scheduleData['day'];
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $day . ' ' . $scheduleData['start_time']);
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $day . ' ' . $scheduleData['end_time']);

            // Kiểm tra xem đã tồn tại lịch làm việc cho ngày này chưa
            $existingSchedule = Schedule::where('store_information_id', $storeId)
                ->where('day', $day)
                ->first();

            if ($existingSchedule) {
                return $this->responseBadRequest(
                    ['Chỉ được đăng ký một bắt đầu và một kết thúc cho mỗi ngày' => $existingSchedule],
                    Response::HTTP_BAD_REQUEST
                );
                // return ['error' => 'Chỉ được đăng ký một bắt đầu và một kết thúc cho mỗi ngày'];
            }

            $openingHours = OpeningHour::where('store_information_id', $storeId)
                ->where('day', $day)
                ->first();

            if (!$openingHours) {
                return $this->responseNotFound(
                    ['Vui lòng đợi ngày bạn chọn hiện chưa cập nhật giờ mở cửa' => $openingHours],
                    Response::HTTP_NOT_FOUND
                );
                // return ['error' => 'Vui lòng đợi ngày bạn chọn hiện chưa cập nhật giờ mở cửa'];
            }

            $storeOpeningTime = Carbon::createFromFormat('Y-m-d H:i:s', $day . ' ' . $openingHours->opening_time);
            $storeClosingTime = Carbon::createFromFormat('Y-m-d H:i:s', $day . ' ' . $openingHours->closing_time);

            if ($startTime->lt($storeOpeningTime) || $endTime->gt($storeClosingTime)) {
                return $this->responseBadRequest(
                    ['Giờ làm phải nằm trong giờ mở cửa của cửa hàng'],
                    Response::HTTP_BAD_REQUEST
                );
                // return ['error' => 'Giờ làm phải nằm trong giờ mở cửa của cửa hàng'];
            }

            // Tiếp tục với các bước xử lý khác như trước...

            $schedule = new Schedule();
            $schedule->user_id = $user->id;
            $schedule->store_information_id = $storeId;
            $schedule->day = $day;
            $schedule->start_time = $startTime;
            $schedule->end_time = $endTime;
            $schedule->created_at = now();
            $schedule->save();
        }

        return $this->responseCreated(
            'đăng ký giờ làm thành công thành công',
            [
                'data' => $schedules,

            ],
        );
    }



    public function getEmployeeBookings()
    {
        $user = Auth::user();

        // Kiểm tra nếu user có role là nhân viên (role = 1)
        if ($user->role !== 1) {
            return response()->json(['error' => 'User không phải là nhân viên'], 403);
        }

        // Lấy ID của nhân viên đang đăng nhập
        $employeeId = $user->id;

        // Lấy tất cả các booking thuộc về nhân viên đang đăng nhập
        $bookings = booking::whereHas('schedule', function ($query) use ($employeeId) {
            $query->where('user_id', $employeeId);
        })->get();

        // Kiểm tra nếu không có booking nào được tìm thấy
        if ($bookings->isEmpty()) {
            return $this->responseNotFound(
                'Hiện bạn không có booking nào',
                Response::HTTP_NOT_FOUND
            );
        }
        return $this->responseSuccess(
            'Xem booking thành công',
            [
                'data' => $bookings,
            ]
        );

    }
    protected function uploadImageIfExists(&$data, $user = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12) . "." . $data['image']->getClientOriginalExtension();
            $data['image']->storeAs('public/images/user', $imageName);

            if ($user && $user->image) {
                Storage::disk('public/images/user')->delete($user->image);
            }

            $data['image'] = $imageName;
        }
    }
}

