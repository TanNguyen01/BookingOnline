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
        $service = Service::with('category')->get();
        return $this->responseSuccess(
            'Lấy danh sách thành công',
            [
                'data' => $service,

            ],
        );
    }

    public function getServiceById($id)

    {

        $service =  Service::with('category')->find($id);
        if (!$service) {
            return $this->responseNotFound(
                'Không tìm thấy dịch vụ',
                Response::HTTP_NOT_FOUND
            );
        } else {
            return $this->responseSuccess(
                'Xem dịch vụ thành công',
                [
                    'data' => $service,

                ],
            );
        }
    }

    public function createService($data)
    {
        $service = Service::create($data);
        return $this->responseCreated(
            'Thêm thành công',
            [
                'data' => $service,

            ],
        );
    }

    public function updateService($id, $data)
    {
        $service = Service::find($id);
        if (!$service) {
            return $this->responseNotFound(
                'Không tìm thấy dịch vụ',
                Response::HTTP_NOT_FOUND
            );
        } else {
            $service->update($data);
            return $this->responseSuccess(
                'cập nhật thành công',
                [
                    'data' => $service,

                ],
            );
        }
    }

    public function deleteService($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return $this->responseNotFound(
                'Không tìm thấy dịch vụ',
                Response::HTTP_NOT_FOUND
            );
        } else {

            $service->delete();
            return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
        }
    }
}
