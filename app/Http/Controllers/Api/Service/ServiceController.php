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
        return $this->serviceService->getAllService();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)

    {
        $service = $this->serviceService->createService($request->all());
       
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

        return $this->serviceService->updateService($id, $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return  $this->serviceService->deleteService($id);
    }
}
