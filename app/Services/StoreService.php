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
        return StoreInformation::find($id);
    }

    public function createStore($data)
    {
        $this->uploadImageIfExists($data);

        return StoreInformation::create($data);
    }

    public function updateStore($id, $data)
    {
        $store = StoreInformation::find($id);
        if ($store) {
            $this->uploadImageIfExists($data, $store);
            $store->update($data);
        }

        return $store;
    }

    public function deleteStore($id)
    {
        $store = StoreInformation::find($id);
        if ($store) {
            if ($store->image) {
                Storage::disk('images_store')->delete($store->image);
            }
            $store->delete();
        }

        return $store;
    }

    protected function uploadImageIfExists(&$data, $store = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('', $imageName, 'images_store');
            if ($store && $store->image) {
                // Xóa ảnh cũ nếu có
                Storage::disk('images_store')->delete($store->image);
            }

            // Tạo URL cho ảnh lưu trong disk 'images_store'
            $data['image'] = Storage::disk('images_store')->url($imageName);
        }
    }
}
