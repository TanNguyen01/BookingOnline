<?php

namespace App\Http\Controllers\Api\OpeningHour;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpeningHourRequest;
use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Models\StoreInformation;
use App\Services\OpeningService;
use App\Traits\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        return $this->responseSuccess(
            __('openingHours.list'),
            [
                'data' => $openingHours,
            ]
        );
    }

    public function show($storeid)
    {
        if (! $storeid) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }
        $store = StoreInformation::find($storeid);
        if (! $store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }
        $openingHours = $store->openingHours()->get(['day', 'opening_time', 'closing_time']);
        if ($openingHours->isEmpty()) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }

        return $this->responseSuccess(
            __('openingHours.show'),
            [
                'data' => $openingHours,
            ]
        );
    }

    public function store(OpeningHourRequest $request, $storeId)
    {
        $store = $this->openingService->createOpeningHours($storeId);
        if (! $store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }

        $openingHoursData = $request->input('opening_hours');

        $existingDays = [];

        foreach ($openingHoursData as $data) {
            // Kiểm tra xem đã có mục nào cho cửa hàng và ngày này chưa
            $existingEntry = OpeningHour::where('store_id', $store->id)
                ->where('day', $data['day'])
                ->first();
            if ($existingEntry) {
                $existingDays[] = $data['day'];
            }
        }
        if (! empty($existingDays)) {
            return $this->responseBadRequest(['Ngày này đã có giờ mở cửa vui lòng kiểm tra lại', $existingDays]);
        }
        foreach ($openingHoursData as $data) {
            OpeningHour::create([
                'store_id' => $store->id,
                'day' => $data['day'],
                'opening_time' => $data['opening_time'],
                'closing_time' => $data['closing_time'],
            ]);
        }

        return $this->responseCreated(__('openingHours.create'), ['data' => $openingHoursData]);
    }

    public function update(OpeningHourRequest $request, $storeId)
    {
        $store = $this->openingService->updateOpeningHours($storeId);
        if (! $store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }

        $openingHoursData = $request->input('opening_hours');

        DB::beginTransaction();

        try {
            foreach ($openingHoursData as $data) {
                $openingHour = OpeningHour::where('store_id', $storeId)
                    ->where('day', $data['day'])
                    ->first();
                if ($openingHour) {
                    $openingHour->update([
                        'opening_time' => $data['opening_time'],
                        'closing_time' => $data['closing_time'],
                    ]);
                } else {
                    OpeningHour::create([
                        'store_id' => $storeId,
                        'day' => $data['day'],
                        'opening_time' => $data['opening_time'],
                        'closing_time' => $data['closing_time'],
                    ]);
                }
                $schedules = Schedule::whereHas('user', function ($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                    ->whereDate('day', $data['day'])
                    ->get();

                foreach ($schedules as $schedule) {
                    $storeOpeningTime = Carbon::parse($data['day'].' '.$data['opening_time']);
                    $scheduleStartTime = Carbon::parse($data['day'].' '.$schedule->start_time);
                    if ($scheduleStartTime->lt($storeOpeningTime)) {
                        $schedule->is_valid = false;
                    } else {
                        $schedule->is_valid = true;
                    }
                    $schedule->save();
                }
            }

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
            Log::error('Lỗi: '.$e->getMessage());

            return $this->responseServerError(Response::HTTP_INTERNAL_SERVER_ERROR, 'Đã xảy ra lỗi. Vui lòng thử lại sau.');
        }
    }

    public function destroy($id)
    {
        $storeId = $this->openingService->deleteOpeningHour($id);
        if (! $storeId) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        } else {
            $today = Carbon::today();
            $deletedRows = OpeningHour::where('store_id', $storeId->id)
                ->where('day', '<', $today)
                ->delete();

            if ($deletedRows > 0) {
                return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
            } else {
                return $this->responseBadRequest([Response::HTTP_BAD_REQUEST, 'không có ngày nào để xóa']);
            }
        }
    }

    public function store5(Request $request, $storeId)
    {

        $openingTime = $request->opening_time;
        $closingTime = $request->closing_time;
        if (! $storeId) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }
        $opening = $this->openingService->createOpeningHours($storeId);
        if (! $opening) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }

        $existingDays = [];
        $currentDate = date('Y-m-d');

        // Thêm 5 ngày liên tiếp bắt đầu từ ngày hôm sau
        for ($i = 1; $i <= 5; $i++) {
            $nextDay = date('Y-m-d', strtotime($currentDate.' + '.$i.' days'));
            $existingNextDayEntry = OpeningHour::where('store_id', $storeId)
                ->where('day', $nextDay)
                ->first();

            if (! $existingNextDayEntry) {
                OpeningHour::create([
                    'store_id' => $storeId,
                    'day' => $nextDay,
                    'opening_time' => $openingTime,
                    'closing_time' => $closingTime,
                ]);
            } else {
                $existingDays[] = $nextDay;
            }
        }

        if (! empty($existingDays)) {
            return $this->responseBadRequest(Response::HTTP_BAD_REQUEST, __('openingHours.exists'), $existingDays);
        }

        return $this->responseCreated(__('openingHours.create'), ['data' => $request->all()]);
    }
}
