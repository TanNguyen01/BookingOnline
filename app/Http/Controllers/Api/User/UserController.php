<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\userRequest;
use App\Services\UserService;
use App\Traits\APIResponse;

use Illuminate\Http\Response;

class UserController extends Controller
{
    use APIResponse;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return $this->responseSuccess(
            'Lấy danh sách người dùng thành công',
            [
                'data' => $users,

            ],
            Response::HTTP_OK
        );
    }

    public function store(userRequest $request)
    {


        $data = $request->all();
        $data['role'] = 1;
        $user = $this->userService->createUser($data);
        return $this->responseSuccess(
            'Thêm thành công dùng thành công',
            [
                'data' => $user,

            ],
            Response::HTTP_OK
        );
    }

    public function show($id)
    {
        return $this->userService->getUserById($id);
    }

    public function update(userRequest $request, $id)
    {

        $data = $request->all();

        $user = $this->userService->updateUser($id, $data);

        return $this->responseSuccess(
            'Cập nhật thành công',
            [
                'data' => $user,

            ],
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);

        return $this->responseSuccess(
            'xóa thành  thành công',
            Response::HTTP_OK
        );
    }
    //
}
