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
    public function listStore()
    {
        $stores = StoreInformation::all();
        return $this->responseSuccess(__('store.list'), ['data' => $stores]);
    }

    public function chooseStore(Request $request)
    {
        $store_id = $request->store_id;
        $store = StoreInformation::find($store_id);
        if (!$store) {
            return $this->responseBadRequest('Cửa hàng không tồn tại.');
        }

        return $this->responseSuccess('Lấy thông tin cửa hàng thành công', ['data' => $store]);
    }

    public function chooseEmployee(Request $request)
    {
        $user_id = $request->user_id;
        $employee = User::where('id', $user_id)->where('role', 1)->first();
        if (!$employee) {
            return $this->responseBadRequest('Người dùng không hợp lệ hoặc không phải là nhân viên.');
        }
        $store_id = $request->store_id;
        $store = StoreInformation::find($store_id);
        if (!$store) {
            return $this->responseBadRequest('Cửa hàng không tồn tại.');
        }
        $isEmployeeOfStore = $employee->schedules()->where('store_information_id', $store_id)->where('is_valid', 1)->exists();
        if (!$isEmployeeOfStore) {
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
        $store_id = $request->store_id;

        $schedules = Schedule::where('user_id', $user_id)
            ->where('store_information_id', $store_id)
            ->where('is_valid', 1)
            ->whereDate('day', '=', $day)
            ->get();
        if ($schedules->isEmpty()) {
            return $this->responseBadRequest('Nhân viên không làm việc vào ngày này.');
        }
        $time_slots = $schedules->map(function ($schedule) {
            return [
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
            ];
        });
        return $this->responseCreated('ngày giờ hợp lệ.', [$time_slots]);
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

            // Tạo đặt chỗ
            $booking = Booking::create([
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

            // Lưu thông tin khách hàng vào bảng Base
            $base = Base::create([
                'booking_id' => $booking->id,
                'email' => $customerEmail,
                'name' => $customerName,
                'date' => $customerDate,
                'phone' => $customerPhone,
                'status' => 'pending', // hoặc trạng thái khác phù hợp
                'note' => $customerNote,
                'created_at' => now(),
            ]);

            // Commit transaction nếu mọi thứ thành công
            DB::commit();

            // In ra tất cả thông tin theo yêu cầu
            $output = [
                'store_name' => $storeData->data->name,
                'store_address' => $storeData->data->address,
                'staff_name' => $employeeData->data->name,
                'staff_id' => $employeeData->data->id,
                'staff_phone' => $employeeData->data->phone,
                'staff_email' => $employeeData->data->email,
                'staff_address' => $employeeData->data->address,
                'service_id' => array_map(function ($service) {
                    return $service->id;
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
            Log::error('Lỗi : ' . $e->getMessage());
            return $this->responseBadRequest('Đã xảy ra lỗi. Vui lòng thử lại sau');
        }
    }
    public function update(Request $request, $id)
    {
        $status = $request->status;
        try {
            DB::beginTransaction();
            $booking = $this->bookingService->getBookingById($id);
            $base = Base::where('booking_id', $booking->id)->first();
            if (($status === 'pending' || $status === 'confirmed' || $status === 'canceled') &&
                ($booking->status === 'doing' || $booking->status === 'done')
            ) {
                return $this->responseBadRequest(__('booking.invalid_status_transition'));
            }
            $booking->status = $status;
            $booking->save();

            // Cập nhật trạng thái của base
            $base->status = $status;
            $base->save();
            // Commit transaction
            DB::commit();
            return $this->responseSuccess(__('booking.update'), ['data' => $base]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('booking.not_found'));
        } catch (\Exception $e) {
            // Xử lý ngoại lệ khác
            DB::rollBack();
            Log::error(__('booking.error_update') . $e->getMessage());
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
        $Booking = $this->bookingService->getBookingByID($id);
        if (! $Booking) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('booking.not_found'));
        }

        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
}
