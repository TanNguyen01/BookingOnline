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
        return $this->userService->getAllUsers();
    }

    public function store(userRequest $request)
    {


        $data = $request->all();
        $data['role'] = 1;
        return $this->userService->createUser($data);
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
        return  $this->userService->deleteUser($id);
    }
}
