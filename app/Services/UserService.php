<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\APIResponse;
use Illuminate\Http\Response;



class UserService
{
    use APIResponse;
    public function getAllUsers()
    {
        return User::query()->get();

    }

    public function getUserById($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return $this->responseBadRequest(
                'Không tìm người dùng',
                Response::HTTP_BAD_REQUEST
            );
        }else{
            return $this->responseSuccess(
                'Xem thông tin người dùng thành công',
                [
                    'data' => $user,

                ],
                Response::HTTP_OK
            );
        }

    }

    public function createUser($data)
    {
        $this->uploadImageIfExists($data);

        return User::create($data);
    }

    public function updateUser($id, $data)
    {
        $user = User::findOrFail($id);

        $this->uploadImageIfExists($data, $user);

        $user->update($data);

        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->image) {
            Storage::disk('public/images/user')->delete($user->image);
        }

        $user->delete();

        return $user;
    }

    protected function uploadImageIfExists(&$data, $user = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12) . "." . $data['image']->getClientOriginalExtension();
            $data['image']->storeAs('public/images/user', $imageName);

            if ($user && $user->image) {
                Storage::disk('public/images/user')->delete($user->image);
            }

            $data['image'] = $imageName;
        }
    }
}
