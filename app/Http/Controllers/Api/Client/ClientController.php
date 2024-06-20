<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\Schedule;
use App\Models\StoreInformation;
use App\Models\User;
use App\Services\ServiceService;
use App\Services\StoreService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    use APIResponse;

    protected $storeService;

    protected $serviceService;

    public function __construct(StoreService $storeService, ServiceService $serviceService)
    {
        $this->storeService = $storeService;
        $this->serviceService = $serviceService;
    }

    public function listStore()
    {
        $stores = $this->storeService->getAllStore();

        return $this->responseSuccess(__('store.list'), ['data' => $stores]);
    }

    public function listService()
    {
        $services = $this->serviceService->getAllService();

        return $this->responseSuccess(__('service.list'), ['data' => $services]);
    }

    public function getUsersByStoreInformation(Request $request)
    {
        $storeId = $request->input('storeId');
        // Kiểm tra xem cửa hàng có tồn tại không
        $store = StoreInformation::find($storeId);
        if (! $store) {
            return $this->responseBadRequest('Không tìm thấy thông tin cửa hàng.');
        }

        // Lấy tất cả người dùng có store_information_id khớp với storeId
        $users = User::where('store_id', $storeId)->get();

        if ($users->isEmpty()) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, 'Không tìm thấy người dùng nào.');
        }

        return $this->responseSuccess('Danh sách người dùng.', ['data' => $users]);
    }

    public function getWorkingHoursByUserAndStore(Request $request)
    {
        $userId = $request->input('userId');
        // Kiểm tra và lấy thông tin người dùng
        $user = User::find($userId);
        if (! $user) {
            return $this->responseBadRequest('Không tìm thấy thông tin người dùng');
        }

        $storeId = $user->store_id;

        // Kiểm tra và lấy thông tin cửa hàng dựa trên store_information_id từ người dùng
        $store = StoreInformation::find($storeId);
        if (! $store) {
            return $this->responseBadRequest('Không tìm thấy thông tin cửa hàng');
        }

        // Lấy lịch làm việc của người dùng dựa trên store_information_id và user_id
        $schedules = Schedule::where('user_id', $userId)
            ->where('is_valid', 1)
            ->get(['day', 'start_time', 'end_time', 'created_at']);

        if ($schedules->isEmpty()) {
            return $this->responseBadRequest('Không có lịch làm việc nào');
        }

        $responseData = [
            'store_id' => $storeId,
            'store_name' => $store->name,
            'schedules' => $schedules,
        ];

        return $this->responseSuccess('Danh sách lịch làm việc.', ['data' => $responseData]);
    }

    public function chooseTime(Request $request)
    {
        $user_id = $request->user_id;
        $day = $request->day;
        $user = User::find($user_id);

        if (! $user) {
            return $this->responseBadRequest('Người dùng không tồn tại.');
        }

        // Lấy lịch làm việc hợp lệ của người dùng trong ngày đã chọn
        $schedules = Schedule::where('user_id', $user_id)
            ->where('is_valid', 1)
            ->whereDate('day', '=', $day)
            ->get(['start_time', 'end_time']);

        if ($schedules->isEmpty()) {
            return $this->responseBadRequest('Nhân viên không làm việc vào ngày này.');
        }

        // Lấy danh sách các booking của user trong ngày đã chọn
        $existing_bookings = Booking::where('user_id', $user_id)
            ->whereDate('day', '=', $day)
            ->pluck('time')
            ->toArray();

        // Thu thập các khoảng thời gian làm việc, thời gian đã book và thời gian chưa book
        $working_time_slots = [];
        $booked_time_slots = [];
        $available_time_slots = [];

        foreach ($schedules as $schedule) {
            $start_time = strtotime($schedule->start_time);
            $end_time = strtotime($schedule->end_time);

            // Bắt đầu từ thời điểm start_time
            $time = $start_time;
            while ($time < $end_time) {
                $time_str = date('H:i:s', $time);

                // Thêm thời gian vào mảng thời gian làm việc
                $working_time_slots[] = $time_str;

                if (in_array($time_str, $existing_bookings)) {
                    // Thêm thời gian vào mảng thời gian đã được book
                    $booked_time_slots[] = $time_str;
                } else {
                    // Thêm thời gian vào mảng thời gian chưa được book
                    $available_time_slots[] = $time_str;
                }

                // Tăng thời gian lên 30 phút (1800 giây)
                $time += 1800;
            }
        }

        // Debugging logs to check the generated slots
        Log::info('Working Time Slots: ', $working_time_slots);
        Log::info('Booked Time Slots: ', $booked_time_slots);
        Log::info('Available Time Slots: ', $available_time_slots);

        // Kiểm tra xem có giờ hẹn khả dụng hay không
        if (empty($available_time_slots)) {
            return $this->responseBadRequest('Không có giờ hẹn khả dụng trong khoảng thời gian làm việc.');
        }

        return $this->responseSuccess('Lấy thành công thông tin làm việc trong ngày của user.', [
            'data' => [
                'working_time_slots' => $working_time_slots,
                'booked_time_slots' => $booked_time_slots,
                'available_time_slots' => $available_time_slots,
            ],
        ]);
    }
}
