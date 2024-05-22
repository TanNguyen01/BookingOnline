<?php

namespace App\Services;

use App\Models\StoreInformation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreService
{
    public function getAllStore()
    {
        return StoreInformation::all();
    }

    public function getStoreById($id)
    {
        $store =  StoreInformation::find($id);
        if (!$store) {
            return response()->json(['status' => 401,
                'error' => 'Không tìm thấy Cửa hàng'
        ]);
        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Xem Cửa hàng thành công',
                'data' => $store,
            ]);
        }

    }

    public function createStore($data)
    {
        $this->uploadImageIfExists($data);

        return StoreInformation::create($data);
    }

    public function updateStore($id, $data)
    {
        $store = StoreInformation::find($id);
        if(!$store){
            return response()->json(['message' => 'Không tồn tại cửa hàng'], 401);

        } else {
        $this->uploadImageIfExists($data, $store);
        $store->update($data);
        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật cửa hàng thành công',
            'data' => $store,
        ]);
    }


    }

    public function deleteStore($id)
    {
        $store = StoreInformation::findOrFail($id);

        if ($store->image) {
            Storage::disk('public')->delete($store->image);
        }

        $store->delete();

        return $store;
    }

    protected function uploadImageIfExists(&$data, $store = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12) . "." . $data['image']->getClientOriginalExtension();
            $data['image']->storeAs('public', $imageName);

            if ($store && $store->image) {
                Storage::disk('public')->delete($store->image);
            }

            $data['image'] = $imageName;
        }
    }
}
