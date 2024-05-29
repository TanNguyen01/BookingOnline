<?php
namespace App\Http\Controllers\Api\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\APIResponse;

class ServiceController extends Controller
{
    use APIResponse;

    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index()
    {
        $services = $this->serviceService->getAllService();
        return $this->responseSuccess('Lấy danh sách thành công', ['data' => $services]);
    }

    public function show($id)
    {
        $service = $this->serviceService->getServiceById($id);
        if (!$service) {
            return $this->responseNotFound('Không tìm thấy dịch vụ', Response::HTTP_NOT_FOUND);
        }
        return $this->responseSuccess('Xem dịch vụ thành công', ['data' => $service]);
    }

    public function store(ServiceRequest $request)
    {
        $service = $this->serviceService->createService($request->all());
        return $this->responseCreated('Thêm thành công', ['data' => $service]);
    }

    public function update(ServiceRequest $request, $id)
    {
        $service = $this->serviceService->updateService($id, $request->all());
        if (!$service) {
            return $this->responseNotFound('Không tìm thấy dịch vụ', Response::HTTP_NOT_FOUND);
        }
        return $this->responseSuccess('Cập nhật thành công', ['data' => $service]);
    }

    public function destroy($id)
    {
        $service = $this->serviceService->deleteService($id);
        if (!$service) {
            return $this->responseNotFound('Không tìm thấy dịch vụ', Response::HTTP_NOT_FOUND);
        }
        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
}
