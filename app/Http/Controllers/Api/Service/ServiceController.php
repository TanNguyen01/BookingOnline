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

    public function index()
    {
        $services = $this->serviceService->getAllService();

        return $this->responseSuccess(__('service.list'), ['data' => $services]);
    }

    public function show($id)
    {
        $service = $this->serviceService->getServiceById($id);
        if (! $service) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('service.not_found'));
        }

        return $this->responseSuccess(__('service.show'), ['data' => $service]);
    }

    public function store(ServiceRequest $request)
    {
        $service = $this->serviceService->createService($request->all());

        return $this->responseCreated(__('service.created'), ['data' => $service]);
    }

    public function update(ServiceRequest $request, $id)
    {
        $service = $this->serviceService->updateService($id, $request->all());
        if (! $service) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('service.not_found'));
        }

        return $this->responseSuccess(__('service.updated'), ['data' => $service]);
    }

    public function destroy($id)
    {
        $service = $this->serviceService->deleteService($id);
        if (! $service) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('service.not_found'));
        }

        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
}
