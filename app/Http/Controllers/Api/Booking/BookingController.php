<?php

namespace App\Http\Controllers\Api\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\ServiceBooking;
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

    public function chooseEmployee(Request $request)
    {
        $user_id = $request->user_id;
        // Kiểm tra xem user có tồn tại và có vai trò là nhân viên không
        $employee = User::where('id', $user_id)->where('role', 1)->first();
        if (! $employee) {
            return $this->responseBadRequest(__('user.not_found'));
        } else {
            return $this->responseCreated(__('user.accept'));
        }
    }

    public function chooseService(Request $request)
    {
        $service_id = $request->service_id;
        $service = Service::find($service_id);
        if (! $service) {
            return $this->responseBadRequest(__('service.not_found'));
        }

        return $this->responseCreated(__('service.accept'));
    }

    public function chooseDate(Request $request)
    {
        $user_id = $request->user_id;
        $day = $request->day;
        $time = $request->time;
        $schedule = Schedule::where('user_id', $user_id)
            ->where('is_valid', 1)
            ->whereDate('day', '=', $day) // Làm việc vào ngày này
            ->where(function ($query) use ($time) {
                $query->whereTime('start_time', '<=', $time) // Thời gian booking sau hoặc bằng thời gian bắt đầu làm việc của nhân viên
                    ->whereTime('end_time', '>=', $time); // Thời gian booking trước hoặc bằng thời gian kết thúc làm việc của nhân viên
            })
            ->first();

        // Kiểm tra nếu không có lịch làm việc phù hợp
        if (! $schedule) {
            return $this->responseBadRequest('Nhân viên không làm việc vào ngày hoặc giờ này.');
        } else {
            return $this->responseCreated('ngày giờ hợp lệ.');
        }
    }

    public function store(BookingRequest $request)
    {
        $user_id = $request->user_id;

        // Kiểm tra và chọn nhân viên
        $employeeResponse = $this->chooseEmployee($request);
        if ($employeeResponse->getStatusCode() !== 201) {
            return $employeeResponse;
        }

        // Chọn dịch vụ
        $serviceResponse = $this->chooseService($request);
        if ($serviceResponse->getStatusCode() !== 201) {
            return $serviceResponse;
        }

        // Chọn ngày và thời gian
        $dateResponse = $this->chooseDate($request);
        if ($dateResponse->getStatusCode() !== 201) {
            return $dateResponse;
        }

        // Nếu mọi thứ đều hợp lệ, tiến hành tạo đặt chỗ
        try {
            DB::beginTransaction();
            // Tạo đặt chỗ
            $booking = Booking::create([
                'user_id' => $user_id,
                'day' => $request->day,
                'time' => $request->time,
                'status' => 'đang chờ xác nhận',
            ]);

            // Tạo liên kết giữa dịch vụ và đặt chỗ
            ServiceBooking::create([
                'service_id' => $request->service_id,
                'booking_id' => $booking->id,
            ]);

            // Commit transaction nếu mọi thứ thành công
            DB::commit();

            return $this->responseCreated(__('booking.create'), ['data' => $booking]);
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            // Log lỗi để kiểm tra
            Log::error(__('booking.error_create').$e->getMessage());

            // Trả về thông báo lỗi
            return $this->responseBadRequest(Response::HTTP_BAD_REQUEST, __('booking.error'));
        }
    }

    public function update(Request $request, $id)
    {
        $status = $request->status;
        try {
            $booking = $this->bookingService->getBookingById($id);
            // Cập nhật trạng thái của booking
            $booking->status = $status;
            $booking->save();

            return $this->responseSuccess(__('booking.update'), ['data' => $booking]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Xử lý khi không tìm thấy booking
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('booking.not_found'));
        } catch (\Exception $e) {
            // Xử lý ngoại lệ khác
            Log::error(__('booking.error_update').$e->getMessage());

            return $this->responseBadRequest(Response::HTTP_BAD_REQUEST, __('booking.error'));
        }
    }

    public function index()
    {
        $bookings = $this->bookingService->getAllBooking();

        return $this->responseSuccess(__('booking.list'), ['data' => $bookings]);
    }

    public function show($id)
    {
        $booking = $this->bookingService->getBookingById($id);
        if (! $booking) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('booking.not_found'));
        }

        return $this->responseSuccess(__('booking.show'), ['data' => $booking]);
    }

    public function destroy($id)
    {
        $booking = $this->bookingService->getBookingById($id);
        if (! $booking) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('booking.not_found'));
        }

        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
}
