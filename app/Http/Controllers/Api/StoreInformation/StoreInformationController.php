<?php

namespace App\Http\Controllers\Api\StoreInformation;

use App\Http\Controllers\Controller;
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpg,png,jpeg',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'message' => 'Thêm thông cửa hàng thất bại',
                'errors' => $validator->errors()->toArray(),
            ]);
        }

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
        $store = $this->storeService->getStoreById($id);
        return response()->json([
            'status' => 200,
            'message' => 'Xem cửa hàng thành công',
            'data' => $store,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'string|email|unique:users,email,' . $id,
            'name' => 'string',
            'password' => 'string|confirmed',
            'role' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'message' => 'Cập nhật thất bại',
                'errors' => $validator->errors()->toArray(),
            ]);
        }

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
