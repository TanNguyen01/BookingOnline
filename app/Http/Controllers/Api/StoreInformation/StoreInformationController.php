<?php

namespace App\Http\Controllers\Api\StoreInformation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInformationRequest;
use App\Http\Requests\UpdateStroreInformationRequest;
use App\Services\StoreService;
use App\Traits\APIResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StoreInformationController extends Controller
{
    use APIResponse;

    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = $this->storeService->getAllStore();

        return $this->responseSuccess(__('store.list'), ['data' => $stores]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInformationRequest $request)
    {
        DB::beginTransaction();

        try {
            $store = $this->storeService->createStore($request->all());

            DB::commit();
            return $this->responseCreated(__('store.created'), ['data' => $store]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseServerError([__('store.error'), 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $store = $this->storeService->getStoreById($id);
        if (!$store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        } else {
            return $this->responseSuccess(__('store.show'), ['data' => $store]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStroreInformationRequest $request, string $id)
    {
        DB::beginTransaction();

    try {
        $store = $this->storeService->updateStore($id, $request->all());
        if (!$store) {
            DB::rollBack();
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        }

        DB::commit();
        return $this->responseSuccess(__('store.updated'), ['data' => $store]);
    } catch (\Exception $e) {
        DB::rollBack();
        return $this->responseServerError([__('store.error'), 'error' => $e->getMessage()]);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $store = $this->storeService->deleteStore($id);
            if (!$store) {
                DB::rollBack();
                return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
            }
            $store->delete();
            DB::commit();
            return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseServerError([__('store.error'), 'error' => $e->getMessage()]);
        }
    }
}
