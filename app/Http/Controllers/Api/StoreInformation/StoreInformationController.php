<?php

namespace App\Http\Controllers\Api\StoreInformation;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInformationRequest;
use App\Services\StoreService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreInformationController extends Controller
{
    use APIResponse;
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function index()
    {

        $stores = $this->storeService->getAllStore();
        return $this->responseSuccess(
            'Lấy danh sách thành công',
            [
                'data' => $stores,

            ],
            Response::HTTP_OK
        );
    }

    public function store(StoreInformationRequest $request)
    {
        $data = $request->all();

        $store = $this->storeService->createStore($data);

        return $this->responseSuccess(
            'thêm cửa hàng thành công',
            [
                'data' => $store,

            ],
            Response::HTTP_OK
        );
    }

    public function show($id)
    {
        return $this->storeService->getStoreById($id);

    }

    public function update(Request $request, $id)
    {

        $data = $request->all();
        $store = $this->storeService->updateStore($id, $data);

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật thành công',
            'data' => $store,
        ]);
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
