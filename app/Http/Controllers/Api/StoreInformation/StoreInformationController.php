<?php

namespace App\Http\Controllers\Api\StoreInformation;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInformationRequest;
use App\Services\StoreService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class StoreInformationController extends Controller
{

    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function index()
    {

        $stores = $this->storeService->getAllStore();
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách Cửa hàng thành công',
            'data' => $stores,
        ], 200);
    }

    public function store(StoreInformationRequest $request)
    {
        $data = $request->all();

        $store = $this->storeService->createStore($data);

        return response()->json([
            'status' => 200,
            'message' => 'Thêm Cửa hàng thành công',
            'data' => $store,
        ]);
    }

    public function show($id)
    {
        return $this->storeService->getStoreById($id);

    }

    public function update(StoreInformationRequest $request, $id)
    {

        $data = $request->all();
        return $this->storeService->updateStore($id, $data);


    }

    public function destroy($id)
    {
        $this->storeService->deleteStore($id);

        return response()->json([
            'status' => 200,
            'message' => 'Xóa thành công',
        ]);
    }
    //
}
