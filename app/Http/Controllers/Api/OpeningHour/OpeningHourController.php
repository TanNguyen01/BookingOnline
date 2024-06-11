<?php

namespace App\Http\Controllers\Api\OpeningHour;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpeningHourRequest;
use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Services\OpeningService;
use App\Traits\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OpeningHourController extends Controller
{
    use APIResponse;

    protected $openingService;

    public function __construct(OpeningService $openingService)
    {
        $this->openingService = $openingService;
    }

    public function index()
    {
        $openingHours = $this->openingService->getAllOpeningHours();

        return response()->responseSuccess(__('openingHours.list'), ['data' => $openingHours], Response::HTTP_OK);
    }

    public function show($storeid)
    {
        $openingHours = $this->openingService->getOpeningHour($storeid);
        if (! $storeid) {
            return $this->responseNotFound(__('store.not_found'), Response::HTTP_NOT_FOUND);
        } else {
            return response()->responseSuccess(__('openingHours.show'), ['data' => $openingHours], Response::HTTP_OK);
        }
    }

    public function store(OpeningHourRequest $request)
    {
        $storeId = $request->input('store_information_id');
        $openingHoursData = $request->input('opening_hours');
        $opening = $this->openingService->createOpeningHours($storeId);
        if (! $storeId) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        } else {
            $existingDays = [];

            foreach ($openingHoursData as $data) {
                // Kiểm tra xem đã có mục nào cho cửa hàng và ngày này chưa
                $existingEntry = OpeningHour::where('store_information_id', $opening->id)
                    ->where('day', $data['day'])
                    ->first();

                // Nếu đã tồn tại mục cho ngày này, thêm vào mảng tồn tại
                if ($existingEntry) {
                    $existingDays[] = $data['day'];
                }
            }

            // Nếu có ngày đã tồn tại, trả về lỗi
            if (! empty($existingDays)) {
                return $this->responseBadRequest(Response::HTTP_BAD_REQUEST, __('openingHours.exists'), $existingDays);
            }

            // Nếu không có ngày nào tồn tại, thêm các ngày mới
            foreach ($openingHoursData as $data) {
                $opentime = OpeningHour::create([
                    'store_information_id' => $opening->id,
                    'day' => $data['day'],
                    'opening_time' => $data['opening_time'],
                    'closing_time' => $data['closing_time'],
                ]);
            }

            return $this->responseCreated(__('openingHours.create'), ['data' => $openingHoursData]);
        }
    }

    public function update(OpeningHourRequest $request)
    {

        $storeId = $request->input('store_information_id');
        $openingHoursData = $request->input('opening_hours');

        // Kiểm tra xem storeId có tồn tại không
        if (! $storeId) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }

        $existingDays = [];
        DB::beginTransaction();

        try {
            foreach ($openingHoursData as $data) {
                // Tìm thông tin giờ mở cửa cho cửa hàng và ngày cụ thể
                $openingHour = OpeningHour::where('store_information_id', $storeId)
                    ->where('day', $data['day'])
                    ->first();

                // Nếu giờ mở cửa tồn tại, cập nhật; nếu không, thêm ngày vào danh sách chưa đăng ký
                if ($openingHour) {
                    $openingHour->update([
                        'opening_time' => $data['opening_time'],
                        'closing_time' => $data['closing_time'],
                    ]);
                } else {
                    $existingDays[] = $data['day'];
                }

                // Tìm tất cả các lịch trình cho cửa hàng và ngày cụ thể
                $schedules = Schedule::where('store_information_id', $storeId)
                    ->whereDate('day', $data['day'])
                    ->get();

                // Xác thực các lịch trình dựa trên giờ mở cửa mới
                foreach ($schedules as $schedule) {
                    $storeOpeningTime = Carbon::parse($data['day'].' '.$data['opening_time']);
                    $scheduleStartTime = Carbon::parse($data['day'].' '.$schedule->start_time);

                    // Đánh dấu lịch trình là không hợp lệ nếu bắt đầu trước giờ mở cửa
                    if ($scheduleStartTime->lt($storeOpeningTime)) {
                        $schedule->is_valid = false;
                    } else {
                        $schedule->is_valid = true;
                    }
                    $schedule->save();

                }
            }

            // Nếu có các ngày chưa đăng ký, hủy bỏ giao dịch và trả về phản hồi lỗi
            if (! empty($existingDays)) {
                return $this->responseBadRequest([Response::HTTP_BAD_REQUEST, 'ngày đó chưa có cửa hàng chưa đăng ký vui longf đợi' => $existingDays]);

            }

            // Cam kết giao dịch nếu mọi thứ thành công
            DB::commit();

            return $this->responseSuccess(
                __('openingHours.update'),
                [
                    'data' => $openingHoursData,
                ]
            );
        } catch (\Exception $e) {
            // Hủy bỏ giao dịch và trả về phản hồi lỗi trong trường hợp xảy ra ngoại lệ
            DB::rollback();

            return $this->responseServerError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $storeId = $this->openingService->deleteOpeningHour($id);
        if (! $storeId) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        } else {
            $today = Carbon::today();
            $deletedRows = OpeningHour::where('store_information_id', $storeId->id)
                ->where('day', '<', $today)
                ->delete();

            if ($deletedRows > 0) {
                return response()->json(null, Response::HTTP_NO_CONTENT);
            } else {
                return response()->json([Response::HTTP_BAD_REQUEST, 'message' => 'Không có ngày nào để xóa']);
            }
        }
    }
}
