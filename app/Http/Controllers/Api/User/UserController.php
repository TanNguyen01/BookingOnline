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
        return $this->responseSuccess('Lấy danh sách người dùng thành công', ['data' => $users]);
    }

    public function store(userRequest $request)
    {


        $data = $request->all();
        $data['role'] = 1;
        $user =  $this->userService->createUser($data);
        return $this->responseCreated(
           'them thanh cong',

            [
                'data' => $user,

            ],
        );
    }

    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        if (!$user) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND,'Không tìm thấy người dùng', );
        }
        return $this->responseSuccess('Xem thông tin người dùng thành công', ['data' => $user]);
    }

    public function update(userRequest $request, $id)
    {

        $user = $this->userService->updateUser($id, $request->all());
        if (!$user) {
            return $this->responseNotFound( Response::HTTP_NOT_FOUND,'Không tìm thấy người dùng',);
        }
        return $this->responseSuccess('Cập nhật thành công', ['data' => $user]);
    }

    public function destroy($id)
    {
        $user = $this->userService->deleteUser($id);
        if (!$user) {
            return $this->responseNotFound( Response::HTTP_NOT_FOUND,'Không tìm thấy người dùng');
        }
        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
}
