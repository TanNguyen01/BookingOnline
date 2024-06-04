<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\OpeningHour;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaffService
{
    public function updateProfile($user, $validatedData)
    {
        if ($user->role !== 1) {
            return null;
        }

        if (! Hash::check($validatedData['current_password'], $user->password)) {
            return false;
        }

        unset($validatedData['current_password']);

        if (isset($validatedData['new_password'])) {
            $validatedData['password'] = bcrypt($validatedData['new_password']);
            unset($validatedData['new_password']);
        }

        $this->uploadImageIfExists($validatedData);
        $user->update($validatedData);

        return $user;
    }

    public function showProfileService($user)
    {
        if ($user->role == 1) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'image' => $user->image,
                'address' => $user->address,
                'phone' => $user->phone,
                'created_at' => $user->created_at,
            ];
        }

        return null;
    }

    public function createSchedule($user, $storeId, $schedules)
    {
        $user = Auth::user();
        if ($user->role !== 1) {
            return null;
        }
        foreach ($schedules as $scheduleData) {
            $day = $scheduleData['day'];
            $startTime = Carbon::createFromFormat('H:i:s', $scheduleData['start_time']);
            $endTime = Carbon::createFromFormat('H:i:s', $scheduleData['end_time']);

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

            if (! $openingHours) {

                return ['error' => 'Vui lòng đợi ngày bạn chọn hiện chưa cập nhật giờ mở cửa'];
            }

            $storeOpeningTime = Carbon::createFromFormat('H:i:s', $openingHours->opening_time);
            $storeClosingTime = Carbon::createFromFormat('H:i:s', $openingHours->closing_time);

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

        return $schedules;
    }

    public function getSchedule()
    {
        return Schedule::all();
    }

    public function getEmployeeBookings($user)
    {
        if ($user->role !== 1) {
            return null;
        }

        $bookings = Booking::whereHas('schedule', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return $bookings;
    }

    protected function uploadImageIfExists(&$data, $user = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('public/images/user', $imageName);

            if ($user && $user->image) {
                Storage::disk('images_user')->delete($user->image);
            }

            $data['image'] = $imageName;
        }
    }
}
