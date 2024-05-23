<?php

namespace App\Services;

use App\Models\Service;
use App\Traits\APIResponse;
use Illuminate\Http\Response;

class ServiceService
{
    use APIResponse;
    public function getAllService()
    {
        return Service::with('category')->get();
    }

    public function getServiceById($id)

    {

        $service =  Service::with('category')->find($id);
        if (!$service) {
            return $this ->responseBadRequest(
            'Không tìm thấy dịch vụ',
            Response::HTTP_BAD_REQUEST);
        }else{
            return $this->responseSuccess(
                'Xem dịch vụ thành công',
                [
                    'data' => $service,

                ],
                Response::HTTP_OK
            );
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
