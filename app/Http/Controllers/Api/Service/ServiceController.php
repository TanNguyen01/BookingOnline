<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
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
        return response()->json([
            'status' => 201,
            'message' => ' lấy danh sách thanh cong',
            'data' => $service
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)

    {
        $service = $this->serviceService->createService($request->all());
        return response()->json([
            'status' => 201,
            'message' => ' them thanh cong',
            'data' => $service
        ]);
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

        return response()->json([
            'status' => 201,
            'message' => ' cập nhật thanh cong',
            'data' => $service
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = $this->serviceService->deleteService($id);
        return response()->json(['message' => 'Xóa thành công']);
    }
}
