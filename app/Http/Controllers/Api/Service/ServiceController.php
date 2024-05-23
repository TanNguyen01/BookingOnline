<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Services\ServiceService;
use App\Traits\APIResponse;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    use APIResponse;
    protected $serviceService;
    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = $this->serviceService->getAllService();
        return $this->responseSuccess(
            'Lấy danh sách thành công',
            [
                'data' => $service,

            ],
            Response::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)

    {
        $service = $this->serviceService->createService($request->all());
        return $this->responseSuccess(
            'Thêm thành công',
            [
                'data' => $service,

            ],
            Response::HTTP_OK
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return  $this->serviceService->getServiceById($id);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, string $id)
    {
        $data = $request->all();

        $service = $this->serviceService->updateService($id, $data);

        return $this->responseSuccess(
            'Cập nhật thành công',
            [
                'data' => $service,

            ],
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = $this->serviceService->deleteService($id);
        return $this->responseSuccess(
            'Xóa thành công',
            [
                'data' => $service,

            ],
            Response::HTTP_OK
        );
    }
}
