<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\userRequest;
use App\Services\UserService;

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
        $data['role'] = 1;
        $user = $this->userService->createUser($data);
        return response()->json([
            'status' => 200,
            'message' => 'Thêm người dùng thành công',
            'data' => $user,
        ]);
    }

    public function show($id)
    {
        return $this->userService->getUserById($id);
    }

    public function update(userRequest $request, $id)
    {

        $data = $request->all();
        return $this->userService->updateUser($id, $data);
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
