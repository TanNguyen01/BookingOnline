<?php

namespace App\Services;

use App\Models\OpeningHour;
use App\Models\StoreInformation;
use Carbon\Carbon;
use Illuminate\Http\Response;
use App\Traits\APIResponse;


class OpeningService
{
    use APIResponse;
    public function getAllOpeningHours()
    {
        $openingHours = OpeningHour::with('storeInformation')->get();
        return $this->responseSuccess(
            'lấy danh sách thành công',
            [
                'data' => $openingHours,
            ],
        );
    }

    public function getOpeningHour($storeid)
    {
        $store = StoreInformation::where('id', $storeid)->first();

        if (!$store) {
            throw new \Exception(' cửa hàng k tồn tai', 401);
        }

        // Lấy thông tin giờ mở cửa của cửa hàng
        $openingHours = $store->openingHours()->get(['day', 'opening_time', 'closing_time']);
        return $this->responseSuccess(
            'lấy giờ mở cửa đóng cửa của cửa hàng theo id thành công',
            [
                'data' => $openingHours,
            ],
                    );
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
            return $this->responseBadRequest(
                ['Ngày giờ mở cửa đã tồn tại' => $existingDays],
                Response::HTTP_BAD_REQUEST
            );
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

        return $this->responseCreated(
            'Thêm ngày giờ mở cửa cho cửa hàng  thành công',
            [
                'data' => $opentime,

            ],
        );
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
            return $this->responseNotFound(
                ['Không thể cập nhật vì ngày không tồn tại' => $existingDays],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->responseSuccess(
            'cập nhật thành công',
            [
                'data' => $openingHour,

            ],
        );
    }

    public function deleteOpeningHour($id)
    {
        $store = StoreInformation::where('id', $id)->first();

        // Kiểm tra xem cửa hàng có tồn tại không
        if (!$store) {
            return $this->responseNotFound(
                'Không tìm thấy cửa hàng',
                Response::HTTP_NOT_FOUND
            );
        }

        // Lấy ngày hiện tại
        $today = Carbon::today();

        // Xóa giờ mở cửa đã hết hạn
        $deletedRows = OpeningHour::where('store_information_id', $store->id)
            ->where('day', '<', $today)
            ->delete();

        if ($deletedRows > 0) {
            return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);

        } else {
            return $this->responseBadRequest(
                'Không có ngày nào để xóa',
                Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
