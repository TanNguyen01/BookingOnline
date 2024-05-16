<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách người dùng thành công',
            'data' => $users,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|confirmed',
            'role' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'message' => 'Đăng ký thất bại',
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        $data = $request->all();

        $user = $this->userService->createUser($data);

        return response()->json([
            'status' => 200,
            'message' => 'Thêm người dùng thành công',
            'data' => $user,
        ]);
    }

    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        return response()->json([
            'status' => 200,
            'message' => 'Xem người dùng thành công',
            'data' => $user,
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

        $user = $this->userService->updateUser($id, $data);

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật người dùng thành công',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'status' => 200,
            'message' => 'Xóa người dùng thành công',
        ]);
    }
    //
}
