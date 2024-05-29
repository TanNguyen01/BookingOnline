<?php

namespace App\Services;

use App\Models\OpeningHour;
use App\Models\Schedule;
use App\Models\StoreInformation;
use Carbon\Carbon;
use Illuminate\Http\Response;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\DB;

class OpeningService
{
    use APIResponse;
    public function getAllOpeningHours()
    {
        return OpeningHour::with('storeInformation')->get();
    }

    public function getOpeningHour($storeid)
    {
        return StoreInformation::findOrFail($storeid)->openingHours()->get(['day', 'opening_time', 'closing_time']);
    }

    public function createOpeningHours($storeid, $openingHoursData)
    {
        $storeInformation = StoreInformation::where('id', $storeid)->firstOrFail();

        $existingDays = [];

        foreach ($openingHoursData as $data) {
            // Kiểm tra xem đã có mục nào cho cửa hàng và ngày này chưa
            $existingEntry = OpeningHour::where('store_information_id', $storeInformation->id)
                ->where('day', $data['day'])
                ->first();

            // Nếu đã tồn tại mục cho ngày này, thêm vào mảng tồn tại
            if ($existingEntry) {
                $existingDays[] = $data['day'];
            }
        }

        // Nếu có ngày đã tồn tại, trả về lỗi
        if (!empty($existingDays)) {

            return ['error' => ['Ngày giờ mở cửa đã tồn tại' => $existingDays]];
        }

        // Nếu không có ngày nào tồn tại, thêm các ngày mới
        foreach ($openingHoursData as $data) {
            $opentime = OpeningHour::create([
                'store_information_id' => $storeInformation->id,
                'day' => $data['day'],
                'opening_time' => $data['opening_time'],
                'closing_time' => $data['closing_time'],
            ]);
        }
        return $openingHoursData;
    }

    public function updateOpeningHours($storeid, $openingHoursData)
    {
        $existingDays = [];
        DB::beginTransaction();

        try {
            foreach ($openingHoursData as $data) {
                $openingHour = OpeningHour::where('store_information_id', $storeid)
                    ->where('day', $data['day'])
                    ->first();

                if ($openingHour) {
                    $openingHour->update([
                        'opening_time' => $data['opening_time'],
                        'closing_time' => $data['closing_time'],
                    ]);
                } else {
                    $existingDays[] = $data['day'];
                }

                $schedules = Schedule::where('store_information_id', $storeid)
                    ->whereDate('day', $data['day'])
                    ->get();

                foreach ($schedules as $schedule) {
                    $storeOpeningTime = Carbon::parse($data['day'] . ' ' . $data['opening_time']);
                    $scheduleStartTime = Carbon::parse($data['day'] . ' ' . $schedule->start_time);

                    if ($scheduleStartTime->lt($storeOpeningTime)) {
                        $schedule->is_valid = false;
                        $schedule->save();
                    }
                }
            }

            if (!empty($existingDays)) {
                return ['error' => ['ngày đó chưa có cửa hàng chưa đăng ký vui longf đợi' => $existingDays]];

            }

            DB::commit();

            return $openingHoursData;
        } catch (\Exception $e) {
            DB::rollback();

            return ['error' => $e->getMessage()];
        }
    }

    public function deleteOpeningHour($id)
    {
        $store = StoreInformation::findOrFail($id);
        $today = Carbon::today();
        $deletedRows = OpeningHour::where('store_information_id', $store->id)
            ->where('day', '<', $today)
            ->delete();

        if ($deletedRows > 0) {
            return true;
        } else {
            return false;
        }
    }
}
