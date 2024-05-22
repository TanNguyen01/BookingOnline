<?php

namespace App\Services;

use App\Models\Service;

class ServiceService
{
    public function getAllService()
    {
        return Service::with('category')->get();
    }

    public function getServiceById($id)

    {

        $service =  Service::with('category')->find($id);
        if (!$service) {
            return response()->json([
                'status' => 401,
                'error' => 'Không tìm thấy dịch vụ'
            ]);
        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Xem dịch vụ thành công',
                'data' => $service,
            ]);
        }
    }

    public function createService($data)
    {
        return Service::create($data);
    }

    public function updateService($id, $data)
    {
        $service = Service::findOrFail($id);
        $service->update($data);
        return $service;
    }

    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return $service;
    }
}
