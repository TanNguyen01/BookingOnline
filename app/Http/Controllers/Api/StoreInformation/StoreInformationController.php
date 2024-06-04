<?php

namespace App\Http\Controllers\Api\StoreInformation;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInformationRequest;
use App\Services\StoreService;
use App\Traits\APIResponse;
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

        return $this->responseSuccess('Xem danh sách hàng thành công', ['data' => $stores]);
    }

    public function show(string $id)
    {
        $store = $this->storeService->getStoreById($id);
        if (! $store) {
            return $this->responseNotFound('Không tìm thấy cửa hàng', Response::HTTP_NOT_FOUND);
        } else {
            return $this->responseSuccess('Xem thông tin cửa hàng thành công', ['data' => $store], Response::HTTP_OK);

        }
    }

    public function store(StoreInformationRequest $request)
    {
        $store = $this->storeService->createStore($request->all());

        return $this->responseCreated('Thêm cửa hàng thành công', ['data' => $store]);
    }

    public function update(StoreInformationRequest $request, $id)
    {
        $store = $this->storeService->updateStore($id, $request->all());
        if (! $store) {
            return $this->responseNotFound('Không tìm thấy cửa hàng', Response::HTTP_NOT_FOUND);
        }

        return $this->responseSuccess('Cập nhật thành công', ['data' => $store]);
    }

    public function destroy($id)
    {
        $store = $this->storeService->deleteStore($id);
        if (! $store) {
            return $this->responseNotFound('Không tìm thấy cửa hàng', Response::HTTP_NOT_FOUND);
        }

        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
    //
}
