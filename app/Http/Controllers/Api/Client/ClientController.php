<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\StoreInformation;
use App\Models\User;
use App\Services\ServiceService;
use App\Services\StoreService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;
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
        if (!$store) {
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
        if (!$user) {
            return $this->responseBadRequest('Không tìm thấy thông tin người dùng');
        }

        $storeId = $user->store_id;

        // Kiểm tra và lấy thông tin cửa hàng dựa trên store_information_id từ người dùng
        $store = StoreInformation::find($storeId);
        if (!$store) {
            return $this->responseBadRequest('Không tìm thấy thông tin cửa hàng');
        }

        // Lấy lịch làm việc của người dùng dựa trên store_information_id và user_id
        $schedules = Schedule::where('user_id', $userId)
            ->get(['day', 'start_time', 'end_time', 'created_at']);

        if ($schedules->isEmpty()) {
            return $this->responseBadRequest('Không có lịch làm việc nào');
        }

        // Chuẩn bị dữ liệu trả về, bao gồm store_information_id, store_name và danh sách lịch làm việc
        $responseData = [
            'store_id' => $storeId,
            'store_name' => $store->name,
            'schedules' => $schedules
        ];

        return $this->responseSuccess('Danh sách lịch làm việc.', ['data' => $responseData]);
    }






    public function chooseTime(Request $request)
    {
        $user_id = $request->user_id;
        $day = $request->day;
        $user = User::find($user_id);

        if (!$user) {
            return $this->responseBadRequest('Người dùng không tồn tại.');
        }

        // Lấy lịch làm việc hợp lệ của người dùng trong ngày đã chọn
        $schedules = Schedule::where('user_id', $user_id)
            ->where('is_valid', 1)
            ->whereDate('day', '=', $day)
            ->get();

        if ($schedules->isEmpty()) {
            return $this->responseBadRequest('Nhân viên không làm việc vào ngày này.');
        }

        // Lấy danh sách các booking của user trong ngày đã chọn
        $existing_bookings = booking::where('user_id', $user_id)
            ->whereDate('day', '=', $day)
            ->get(['time'])
            ->map(function ($booking) use ($schedules) {
                $status = 'booked';
                foreach ($schedules as $schedule) {
                    $start_time = strtotime($schedule->start_time);
                    $end_time = strtotime($schedule->end_time);
                    $booking_time = strtotime($booking->time);

                    if ($booking_time >= $start_time && $booking_time < $end_time) {
                        $status = 'booked';
                        break;
                    }
                }

                return [
                    'time' => $booking->time,
                    'status' => $status,
                ];
            });

        // Thu thập các khoảng thời gian làm việc và các giờ hẹn khả dụng
        $available_time_slots = [];
        foreach ($schedules as $schedule) {
            $start_time = strtotime($schedule->start_time);
            $end_time = strtotime($schedule->end_time);

            // Bắt đầu từ thời điểm start_time
            $time = $start_time;
            while ($time < $end_time) {
                $time_str = date('H:i:s', $time);
                $status = 'available';

                // Kiểm tra xem giờ hẹn này có trùng với các booking đã tồn tại không
                foreach ($existing_bookings as $existing_booking) {
                    if ($existing_booking['time'] === $time_str) {
                        $status = 'booked';
                        break;
                    }
                }

                // Chỉ thêm thời gian nếu không bị đánh dấu là 'booked'
                if ($status != 'booked') {
                    $available_time_slots[] = [
                        'time' => $time_str,
                        'status' => $status
                    ];
                }

                // Tăng thời gian lên 30 phút (1800 giây)
                $time += 1800;
            }
        }

        // Debugging logs to check the generated slots
        Log::info('Available Time Slots: ', $available_time_slots);

        // Kiểm tra xem có giờ hẹn khả dụng hay không
        $is_valid_time = false;
        foreach ($available_time_slots as $slot) {
            if ($slot['status'] === 'available') {
                $is_valid_time = true;
                break;
            }
        }
        if (!$is_valid_time) {
            return $this->responseBadRequest('Không có giờ hẹn khả dụng trong khoảng thời gian làm việc.');
        }
        return $this->responseCreated('Ngày giờ hợp lệ.', [
            'existing_bookings' => $existing_bookings,
            'available_time_slots' => $available_time_slots,
        ]);
    }
}
