<?php

namespace App\Services;

use App\Models\OpeningHour;
use App\Models\StoreInformation;
use Carbon\Carbon;

class OpeningService
{
    public function getAllOpeningHours()
    {
        return OpeningHour::with('storeInformation')->get();
    }

    public function getOpeningHour($storeid)
    {
        $store = StoreInformation::where('id', $storeid)->first();

        if (!$store) {
            throw new \Exception(' cửa hàng k tồn tai', 401);
        }

        // Lấy thông tin giờ mở cửa của cửa hàng
        $openingHours = $store->openingHours()->get(['day', 'opening_time', 'closing_time']);
        return $openingHours;
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
            return [
                'status' => 401,
                'message' => '401 ,Ngày giờ mở cửa đã tồn tại.',
                'existing_days' => $existingDays
            ];
        }

        // Nếu không có ngày nào tồn tại, thêm các ngày mới
        foreach ($openingHoursData as $data) {
            OpeningHour::create([
                'store_information_id' => $storeInformation->id,
                'day' => $data['day'],
                'opening_time' => $data['opening_time'],
                'closing_time' => $data['closing_time'],
            ]);
        }

        return [
            'status' => 201,
            'message' => 'Giờ làm của cửa hàng đã được thêm.'
        ];
    }




    public function updateOpeningHours($storeid, $openingHoursData)
    {
        $existingDays = [];

        foreach ($openingHoursData as $data) {
            // Tìm mục mở cửa hiện tại cho cửa hàng và ngày này
            $openingHour = OpeningHour::where('store_information_id', $storeid)
                                      ->where('day', $data['day'])
                                      ->first();

            // Nếu tìm thấy mục, cập nhật giờ mở cửa và giờ đóng cửa
            if ($openingHour) {
                $openingHour->update([
                    'opening_time' => $data['opening_time'],
                    'closing_time' => $data['closing_time'],
                ]);
            } else {
                // Nếu không tìm thấy, thêm ngày vào mảng tồn tại
                $existingDays[] = $data['day'];
            }
        }

        // Nếu có ngày không tìm thấy, trả về lỗi
        if (!empty($existingDays)) {
            return [
                'status' => false,
                'message' => 'Không thể cập nhật vì ngày không tồn tại.',
                'missing_days' => $existingDays
            ];
        }

        return [
            'status' => true,
            'message' => 'Giờ làm của cửa hàng đã được cập nhật.'
        ];
    }

        // Trả về thông báo thành công




    public function deleteOpeningHour($id)
    {
        $store = StoreInformation::where('id', $id)->first();

        // Kiểm tra xem cửa hàng có tồn tại không
        if (!$store) {
            return ['error' => 'Cửa hàng không tồn tại'];
        }

        // Lấy ngày hiện tại
        $today = Carbon::today();

        // Xóa giờ mở cửa đã hết hạn
        $deletedRows = OpeningHour::where('store_information_id', $store->id)
            ->where('day', '<', $today)
            ->delete();

        if ($deletedRows > 0) {
            return [
                'message' => 'Đã xóa các giờ mở cửa hết hạn',
                'deleted_rows' => $deletedRows
            ];
        } else {
            return ['message' => 'Không có giờ mở cửa nào để xóa'];
        }
    }
}
