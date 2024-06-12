<?php

namespace App\Http\Controllers\Api\StoreInformation;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInformationRequest;
use App\Http\Requests\UpdateStroreInformationRequest;
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

        return $this->responseSuccess(__('store.list'), ['data' => $stores]);
    }

    public function show(string $id)
    {
        $store = $this->storeService->getStoreById($id);
        if (! $store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        } else {
            return $this->responseSuccess(__('store.show'), ['data' => $store]);

        }
    }

    public function store(StoreInformationRequest $request)
    {
        $store = $this->storeService->createStore($request->all());

        return $this->responseCreated(__('store.created'), ['data' => $store]);
    }

    public function update(UpdateStroreInformationRequest $request, $id)
    {
        $store = $this->storeService->updateStore($id, $request->all());
        if (! $store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }

        return $this->responseSuccess(__('store.updated'), ['data' => $store]);
    }

    public function destroy($id)
    {
        $store = $this->storeService->deleteStore($id);
        if (! $store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }

        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
    //
}
