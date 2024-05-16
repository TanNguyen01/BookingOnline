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

    public function getOpeningHour($storeName)
    {
        $store = StoreInformation::where('name', $storeName)->first();

        if (!$store) {
            throw new \Exception('Cửa hàng không tồn tại', 404);
        }

        // Lấy thông tin giờ mở cửa của cửa hàng
        $openingHours = $store->openingHours()->get(['day', 'opening_time', 'closing_time']);

        return $openingHours;
    }

    public function createOpeningHour($data, $storeName)
    {
        $storeInformation = StoreInformation::where('name', $storeName)->firstOrFail();
        $openingHour = new OpeningHour;
        $openingHour->store_information_id = $storeInformation->id;
        $openingHour->day = $data['day'];
        $openingHour->opening_time = $data['opening_time'];
        $openingHour->closing_time = $data['closing_time'];
        $openingHour->save();

        return $openingHour;
    }

    public function updateOpeningHours($storeName, $data)
    {
        // Tìm cửa hàng theo tên
        $store = StoreInformation::where('name', $storeName)->first();

        // Kiểm tra xem cửa hàng có tồn tại không
        if (!$store) {
        }

        // Tìm hoặc tạo mới bản ghi mở cửa theo cửa hàng và ngày
        $openingHour = OpeningHour::updateOrCreate(
            ['store_information_id' => $store->id, 'day' => $data['day']],
            ['opening_time' => $data['opening_time'], 'closing_time' => $data['closing_time']]
        );

        // Trả về thông báo thành công
    }



    public function deleteOpeningHour($storeName)
    {
        $store = StoreInformation::where('name', $storeName)->first();

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
