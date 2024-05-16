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
        return StoreInformation::findOrFail($id);
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
