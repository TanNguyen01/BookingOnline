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

        return $this->storeService->getAllStore();

    }

    public function store(StoreInformationRequest $request)
    {
        $data = $request->all();

        return $this->storeService->createStore($data);


    }

    public function show($id)
    {
        return $this->storeService->getStoreById($id);

    }

    public function update(Request $request, $id)
    {

        $data = $request->all();
        return $this->storeService->updateStore($id, $data);


    }

    public function destroy($id)
    {
       return $this->storeService->deleteStore($id);


    }
    //
}
