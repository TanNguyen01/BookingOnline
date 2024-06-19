<?php

namespace App\Http\Controllers\Api\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Base;
use App\Models\booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\StoreInformation;
use App\Models\User;
use App\Services\BookingService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    use APIResponse;

    public function chooseStore(Request $request)
    {
        $user_id = $request->user_id;
        $user = User::find($user_id);

        if (! $user) {
            return $this->responseBadRequest('Người dùng không tồn tại.');
        }

        $store_id = $user->store_id;
        $store = StoreInformation::find($store_id);

        if (! $store) {
            return $this->responseBadRequest('Thông tin cửa hàng không tồn tại.');
        }

        return $this->responseSuccess('Lấy thông tin cửa hàng thành công', ['data' => $store]);
    }

    public function chooseEmployee(Request $request)
    {
        $user_id = $request->user_id;
        $employee = User::where('id', $user_id)->where('role', 1)->first();

        if (! $employee) {
            return $this->responseBadRequest('Người dùng không hợp lệ hoặc không phải là nhân viên.');
        }

        $store_id = $employee->store_id;
        $store = StoreInformation::find($store_id);

        if (! $store) {
            return $this->responseBadRequest('Thông tin cửa hàng không tồn tại.');
        }

        // Kiểm tra xem nhân viên có được gán cho cửa hàng này hay không
        $isEmployeeOfStore = $employee->store_id;

        if (! $isEmployeeOfStore) {
            return $this->responseBadRequest('Người dùng không được gán cho cửa hàng này.');
        }

        return $this->responseSuccess('Người dùng hợp lệ là nhân viên của cửa hàng.', ['data' => $employee]);
    }

    public function chooseService(Request $request)
    {
        $service_ids = $request->service_ids;
        $services = Service::whereIn('id', $service_ids)->get();
        if (count($services) != count($service_ids)) {
            return $this->responseBadRequest('Một số dịch vụ không tồn tại.');
        }

        return $this->responseSuccess('Các dịch vụ hợp lệ.', ['services' => $services]);
    }

    public function chooseDate(Request $request)
    {
        $user_id = $request->user_id;
        $day = $request->day;
        $appointment_time = $request->time;
        $user = User::find($user_id);
        if (! $user) {
            return $this->responseBadRequest('Người dùng không tồn tại.');
        }
        // Lấy thông tin cửa hàng của người dùng
        $store_id = $user->store_id;
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
            ->pluck('time')
            ->toArray();

        // Kiểm tra xem giờ hẹn mới có trùng với các booking đã tồn tại không
        if (in_array($appointment_time, $existing_bookings)) {
            return $this->responseBadRequest('Giờ hẹn đã được đặt. Vui lòng chọn lại.');
        }

        // Kiểm tra xem giờ hẹn mới có nằm trong khoảng thời gian làm việc nào không
        $valid_schedule = $schedules->first(function ($schedule) use ($appointment_time) {
            return $appointment_time >= $schedule->start_time && $appointment_time <= $schedule->end_time;
        });

        if (! $valid_schedule) {
            return $this->responseBadRequest('Giờ hẹn không nằm trong khoảng thời gian làm việc.');
        }

        // Thu thập các khoảng thời gian làm việc
        $time_slots = $schedules->flatMap(function ($schedule) {
            return [
                [
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ],
            ];
        });

        // Kiểm tra xem giờ hẹn mới có cách nhau ít nhất 30 phut so với các giờ hẹn đã đặt
        $is_valid_time_slot = true;
        foreach ($existing_bookings as $existing_booking) {
            $existing_timestamp = strtotime($existing_booking);
            $appointment_timestamp = strtotime($appointment_time);

            if (abs($existing_timestamp - $appointment_timestamp) < 1800) { // 3600 giây = 1 tiếng
                $is_valid_time_slot = false;
                break;
            }
        }

        if (! $is_valid_time_slot) {
            return $this->responseBadRequest('Nhân viên đang có lịch vào giờ này vui lòng chọn giờ khác');
        }

        return $this->responseCreated('Ngày giờ hợp lệ.', ['time_slots' => $time_slots]);
    }

    public function store(BookingRequest $request)
    {
        $user_id = $request->user_id;

        // Kiểm tra cửa hàng
        $storeResponse = $this->chooseStore($request);
        if ($storeResponse->getStatusCode() !== 200) {
            return $storeResponse;
        }
        $storeData = $storeResponse->getData()->data;
        //  dd($storeData);

        // Kiểm tra nhân viên
        $employeeResponse = $this->chooseEmployee($request);
        if ($employeeResponse->getStatusCode() !== 200) {
            return $employeeResponse;
        }
        $employeeData = $employeeResponse->getData()->data;
        // dd( $storeData);

        // Kiểm tra ngày và giờ
        $dateResponse = $this->chooseDate($request);
        if ($dateResponse->getStatusCode() !== 201) {
            return $dateResponse;
        }

        // Kiểm tra dịch vụ
        $serviceResponse = $this->chooseService($request);
        if ($serviceResponse->getStatusCode() !== 200) {
            return $serviceResponse;
        }
        $services = $serviceResponse->getData()->data->services;

        // Lấy thông tin khách hàng từ $request
        $customerName = $request->customer_name;
        $customerDate = $request->customer_date;
        $customerPhone = $request->customer_phone;
        $customerNote = $request->customer_note;
        $customerEmail = $request->customer_email;

        try {
            DB::beginTransaction();
            $booking = booking::create([
                'user_id' => $user_id,
                'day' => $request->day,
                'time' => $request->time,
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // Lưu dịch vụ vào bảng ServiceBooking
            foreach ($services as $service) {
                ServiceBooking::create([
                    'service_id' => $service->id,
                    'booking_id' => $booking->id,
                    'created_at' => now(),
                ]);
            }
            // dd($storeData->data->name);
            // Lưu thông tin khách hàng vào bảng Base
            $base = Base::create([
                'booking_id' => $booking->id,
                'store_name' => $storeData->data->name,
                'staff_name' => $employeeData->data->name,
                'email' => $customerEmail,
                'name' => $customerName,
                'date' => $customerDate,
                'phone' => $customerPhone,
                'status' => 'pending',
                'note' => $customerNote,
                'created_at' => now(),
            ]);
            DB::commit();
            $output = [
                'store_name' => $storeData->data->name,
                'store_address' => $storeData->data->address,
                'staff_name' => $employeeData->data->name,
                'staff_id' => $employeeData->data->id,
                'staff_phone' => $employeeData->data->phone,
                'staff_email' => $employeeData->data->email,
                'staff_address' => $employeeData->data->address,
                'service_name' => array_map(function ($service) {
                    return $service->name;
                }, $services),
                'time_order' => $booking->time,
                'date_order' => $booking->day,
                'customer_name' => $base->name,
                'customer_phone' => $base->phone,
                'customer_date' => $base->date,
                'customer_note' => $base->note,
                'customer_email' => $customerEmail,
            ];

            return $this->responseCreated('Thêm thành công', ['data' => $output]);
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();
            Log::error('Lỗi : '.$e->getMessage());

            return $this->responseBadRequest('Đã xảy ra lỗi. Vui lòng thử lại sau');
        }
    }

    public function update(Request $request, $id)
    {
        $status = $request->status;
        try {
            DB::beginTransaction();
            $base = Base::findOrFail($id);
            if (($status === 'pending' || $status === 'confirmed' || $status === 'canceled') &&
                ($base->status === 'doing' || $base->status === 'done')
            ) {
                return $this->responseBadRequest(__('Không thể cập nhật trạng thái'));
            }
            $base->status = $status;
            $base->save();

            // Cập nhật trạng thái của base
            $booking = $base->booking;
            if ($booking) {
                $booking->status = $status;
                $booking->save();
            }
            // Commit transaction
            DB::commit();

            return $this->responseSuccess(__('booking.update'), ['data' => $base]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('booking.not_found'));
        } catch (\Exception $e) {
            // Xử lý ngoại lệ khác
            DB::rollBack();
            Log::error(__('booking.error_update').$e->getMessage());

            return $this->responseBadRequest(Response::HTTP_BAD_REQUEST, __('booking.error'));
        }
    }

    public function index()
    {
        $Bases = $this->bookingService->getAllBases();

        return $this->responseSuccess(__('booking.list'), ['data' => $Bases]);
    }

    public function show($id)
    {
        $Base = $this->bookingService->getBaseByID($id);
        if (! $Base) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('booking.not_found'));
        }

        return $this->responseSuccess(__('booking.show'), ['data' => $Base]);
    }

    public function destroy($id)
    {
        $Base = $this->bookingService->getBaseByID($id);
        if (! $Base) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('booking.not_found'));
        }

        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
}
