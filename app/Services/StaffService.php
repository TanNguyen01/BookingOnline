<?php

namespace App\Services;

use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffService
{
    public function updateProfile($user, $validatedData)
    {
        // Kiểm tra xem người dùng có quyền là nhân viên không
        if ($user->role !== 1) {
            return response()->json(['error' => 'Bạn không có quyền cập nhật hồ sơ'], 401);
        }

        // Kiểm tra xem mật khẩu hiện tại có chính xác không
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json(['error' => 'Mật khẩu hiện tại không chính xác'], 401);
        }

        // Loại bỏ trường mật khẩu hiện tại để tránh lưu vào cơ sở dữ liệu
        unset($validatedData['current_password']);

        // Nếu có mật khẩu mới, mã hóa và cập nhật vào cơ sở dữ liệu
        if (isset($validatedData['new_password'])) {
            $validatedData['password'] = bcrypt($validatedData['new_password']);
            unset($validatedData['new_password']);
        }

        // Cập nhật thông tin hồ sơ của người dùng
        $user->update($validatedData);
    }


    public function showProfileService()
    {
        $user = Auth::user();

        if ($user->role == 1) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'image' => $user->image,
                'address' => $user->address,
                'phone' => $user->phone,
                'created_at' => $user->created_at
            ];
        } else {
            return null;
        }
    }

    public function createSchedule($user, $storeId, $schedules)
    {

        foreach ($schedules as $scheduleData) {
            $day = $scheduleData['day'];
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $day . ' ' . $scheduleData['start_time']);
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $day . ' ' . $scheduleData['end_time']);

            // Kiểm tra xem đã tồn tại lịch làm việc cho ngày này chưa
            $existingSchedule = Schedule::where('store_information_id', $storeId)
                ->where('day', $day)
                ->first();

            if ($existingSchedule) {
                return ['error' => 'Chỉ được đăng ký một bắt đầu và một kết thúc cho mỗi ngày'];
            }

            $openingHours = OpeningHour::where('store_information_id', $storeId)
                ->where('day', $day)
                ->first();

            if (!$openingHours) {
                return ['error' => 'Vui lòng đợi ngày bạn chọn hiện chưa cập nhật giờ mở cửa'];
            }

            $storeOpeningTime = Carbon::createFromFormat('Y-m-d H:i:s', $day . ' ' . $openingHours->opening_time);
            $storeClosingTime = Carbon::createFromFormat('Y-m-d H:i:s', $day . ' ' . $openingHours->closing_time);

            if ($startTime->lt($storeOpeningTime) || $endTime->gt($storeClosingTime)) {
                return ['error' => 'Giờ làm phải nằm trong giờ mở cửa của cửa hàng'];
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

        return ['message' => 'Lịch làm việc đã được thêm thành công'];
    
    }
}

