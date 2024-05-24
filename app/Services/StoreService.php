<?php

namespace App\Services;

use App\Models\StoreInformation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Traits\APIResponse;
use Illuminate\Support\Str;

class StoreService
{
    use APIResponse;
    public function getAllStore()
    {
        $store =  StoreInformation::all();
        return $this->responseSuccess(
            'Xem danh sách hàng thành công',
            [
                'data' => $store,

            ],
        );
    }

    public function getStoreById($id)
    {
        $store =  StoreInformation::find($id);
        if (!$store) {
            return $this->responseNotFound(
                'Không tìm thấy cửa hàng',
                Response::HTTP_NOT_FOUND
            );
        } else {
            return $this->responseSuccess(
                'Xem thông tin cửa hàng thành công',
                [
                    'data' => $store,

                ],
                Response::HTTP_OK
            );
        }
    }

    public function createStore($data)
    {
        $this->uploadImageIfExists($data);
        $store = StoreInformation::create($data);
        return $this->responseCreated(
            'thêm cửa hàng thành công',
            [
                'data' => $store,

            ],
        );
    }

    public function updateStore($id, $data)
    {
        $store = StoreInformation::find($id);
        if (!$store) {
            return $this->responseNotFound(
                'Không tìm thấy cửa hàng',
                Response::HTTP_NOT_FOUND
            );
        } else {
            $this->uploadImageIfExists($data, $store);
            $store->update($data);
            return $this->responseSuccess(
                'cập nhật thành công',
                [
                    'data' => $store,

                ],
            );
        }
    }

    public function deleteStore($id)
    {
        $store = StoreInformation::find($id);
        if (!$store) {
            return $this->responseNotFound(
                'Không tìm thấy cửa hàng',
                Response::HTTP_NOT_FOUND
            );
        } else {
            if ($store->image) {
                Storage::disk('images_store')->delete($store->image);
            }
            $store->delete();
            return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
        }
    }

    protected function uploadImageIfExists(&$data, $store = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12) . "." . $data['image']->getClientOriginalExtension();
            $data['image']->storeAs('public/images/store', $imageName);

            if ($store && $store->image) {
                Storage::disk('public/images/store')->delete($store->image);
            }

            $data['image'] = $imageName;
        }
    }
}
