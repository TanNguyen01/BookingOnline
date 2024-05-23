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
        return StoreInformation::all();
    }

    public function getStoreById($id)
    {
        $store =  StoreInformation::find($id);
        if (!$store) {
            return $this->responseBadRequest(
                'Không tìm thấy cửa hàng',
                Response::HTTP_BAD_REQUEST
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

        return StoreInformation::create($data);
    }

    public function updateStore($id, $data)
    {
        $store = StoreInformation::findOrFail($id);

        $this->uploadImageIfExists($data, $store);

        $store->update($data);

        return $store;
    }

    public function deleteStore($id)
    {
        $store = StoreInformation::findOrFail($id);

        if ($store->image) {
            Storage::disk('public/images/store')->delete($store->image);
        }

        $store->delete();

        return $store;
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
