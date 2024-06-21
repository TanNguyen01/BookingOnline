<?php
namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleRequest;
use App\Http\Requests\StaffRequest;
use App\Models\Booking;
use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Models\StoreInformation;
use App\Services\StaffService;
use App\Traits\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();

        try {
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
            DB::commit();

            return $this->responseSuccess(__('user.updated'), ['data' => $user]);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->responseBadRequest(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function showProfile()
    {

        $profile = $this->staffService->staffService();

        return $this->responseSuccess(__('user.show'), [
            'id' => $profile->id,
            'email' => $profile->email,
            'name' => $profile->name,
            'image' => $profile->image,
            'address' => $profile->address,
            'phone' => $profile->phone,
            'store_id' => $profile->store_id,
            'created_at' => $profile->created_at,
        ]);
    }

    public function createSchedule(ScheduleRequest $request)
    {
        $user = $this->staffService->staffService();
        $schedules = $request->input('schedules');

        // Sử dụng transaction để đảm bảo tất cả các lịch trình đều hợp lệ trước khi lưu
        DB::beginTransaction();
        try {
            foreach ($schedules as $scheduleData) {
                $day = $scheduleData['day'];
                $startTime = Carbon::createFromFormat('H:i:s', $scheduleData['start_time']);
                $endTime = Carbon::createFromFormat('H:i:s', $scheduleData['end_time']);

                // Kiểm tra giờ mở cửa của cửa hàng
                $openingHours = OpeningHour::where('store_id', $user->store_id)
                    ->where('day', $day)
                    ->first();

                if (! $openingHours) {
                    DB::rollBack();

                    return $this->responseNotFound([Response::HTTP_NOT_FOUND, 'Ngày này cửa hàng chưa cập nhật giờ mở cửa, vui lòng đợi', $day]);
                }

                $storeOpeningTime = Carbon::createFromFormat('H:i:s', $openingHours->opening_time);
                $storeClosingTime = Carbon::createFromFormat('H:i:s', $openingHours->closing_time);

                if ($startTime->lt($storeOpeningTime) || $endTime->gt($storeClosingTime)) {
                    DB::rollBack();

                    return $this->responseNotFound([Response::HTTP_NOT_FOUND, 'Giờ bắt đầu phải nằm trong giờ mở cửa và đóng cửa', $day]);
                }

                // Kiểm tra xem đã tồn tại lịch làm việc cho ngày này chưa
                $existingSchedule = Schedule::where('user_id', $user->id)
                    ->where('day', $day)
                    ->first();

                if ($existingSchedule) {
                    // Cập nhật lịch trình hiện có
                    $existingSchedule->start_time = $startTime;
                    $existingSchedule->end_time = $endTime;
                    $existingSchedule->is_valid = 1;
                    $existingSchedule->updated_at = now();
                    $existingSchedule->save();
                } else {
                    // Tạo lịch trình mới
                    Schedule::create([
                        'user_id' => $user->id,
                        'day' => $day,
                        'start_time' => $startTime,
                        'is_valid' => 1,
                        'end_time' => $endTime,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            DB::commit();

            return $this->responseCreated('Đăng ký hoặc cập nhật giờ làm thành công', ['data' => $schedules]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseNotFound([Response::HTTP_INTERNAL_SERVER_ERROR, 'Đã xảy ra lỗi trong quá trình đăng ký hoặc cập nhật giờ làm', $e->getMessage()]);
        }
    }

    public function getEmployeeBookings()
    {
        $user = $this->staffService->staffService();

        // Lấy danh sách các booking của nhân viên dựa trên user_id và lấy thêm thông tin store_name
        $bookings = Booking::where('user_id', $user->id)
            ->with(['user.storeInformation:id,name,address'])
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'day' => $booking->day,
                    'time' => $booking->time,
                    'status' => $booking->status,
                    'store_name' => $booking->user->storeInformation->name,
                    'store_address' => $booking->user->storeInformation->address,

                ];
            });

        if ($bookings->isEmpty()) {
            return $this->responseNotFound('Hiện bạn không có booking nào', Response::HTTP_NOT_FOUND);
        } else {
            return $this->responseSuccess('Xem booking thành công', ['data' => $bookings]);
        }
    }

    public function seeSchedule()
    {
        $user = $this->staffService->staffService();
        $schedules = Schedule::where('user_id', $user->id)
            ->with(['user.storeInformation:id,name,address'])
            ->get()
            ->map(function ($schedule) {
                $error = $schedule->is_valid == 0 ? 'Vui lòng kiểm tra lại giờ mở cửa của cửa hàng đã được thay đổi vui lòng đăng ký lại' : null;

                return [
                    'id' => $schedule->id,
                    'user_id' => $schedule->user_id,
                    'store_name' => $schedule->user->storeInformation->name,
                    'store_address' => $schedule->user->storeInformation->address,
                    'day' => $schedule->day,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'is_valid' => $schedule->is_valid,
                    'created_at' => $schedule->created_at,
                    'error' => $error,
                ];
            });

        if ($schedules->isEmpty()) {
            return $this->responseNotFound('Hiện bạn không có lịch làm nào', Response::HTTP_NOT_FOUND);
        } else {
            $schedules->contains(function ($schedule) {
                return $schedule['is_valid'] == 0;
            });

            return $this->responseSuccess('Xem lịch làm thành công', ['data' => $schedules]);
        }
    }

    public function viewStoreOpeningHours()
    {
        // Lấy thông tin người dùng hiện tại
        $user = $this->staffService->staffService();

        // Lấy thông tin cửa hàng
        $store = StoreInformation::find($user->store_id);
        if (! $store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, 'Cửa hàng không tồn tại');
        }

        // Lấy giờ mở cửa của cửa hàng
        $openingHours = OpeningHour::where('store_id', $store->id)->get();
        if ($openingHours->isEmpty()) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, 'Cửa hàng chưa cập nhật giờ mở cửa');
        }

        // Định dạng giờ mở cửa
        $formattedHours = $openingHours->map(function ($hour) {
            return [
                'day' => $hour->day,
                'opening_time' => $hour->opening_time,
                'closing_time' => $hour->closing_time,
            ];
        });

        // Bao gồm thông tin cửa hàng trong phản hồi
        return $this->responseSuccess('Giờ mở cửa của cửa hàng', [
            'store_id' => $store->id,
            'store_name' => $store->name,
            'data' => $formattedHours,
        ]);
    }
}
