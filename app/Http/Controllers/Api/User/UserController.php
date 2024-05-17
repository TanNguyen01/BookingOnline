<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\userRequest;
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

    public function store(userRequest $request)
    {


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

    public function update(userRequest $request, $id)
    {

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
