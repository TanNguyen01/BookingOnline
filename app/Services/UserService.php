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
        $users = User::query()->get();
        return $this->responseSuccess(
            'Lấy danh sách người dùng thành công',
            [
                'data' => $users,

            ],
        );

    }

    public function getUserById($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return $this->responseNotFound(
                'Không tìm người dùng',
                Response::HTTP_NOT_FOUND
            );
        }else{
            return $this->responseSuccess(
                'Xem thông tin người dùng thành công',
                [
                    'data' => $user,

                ],
            );
        }

    }

    public function createUser($data)
    {
        $this->uploadImageIfExists($data);

        $user =  User::create($data);
        return $this->responseCreated(
            'thêm người dùng công',
            [
                'data' => $user,

            ],
        );
    }

    public function updateUser($id, $data)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->responseNotFound(
                'Không tìm người dùng',
                Response::HTTP_NOT_FOUND
            );
        } else {
            $this->uploadImageIfExists($data, $user);
            $user->update($data);
            return $this->responseSuccess(
                'cập nhật thành công',
                [
                    'data' => $user,

                ],
            );
        }
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->responseNotFound(
                'Không tìm người dùng',
                Response::HTTP_NOT_FOUND
            );
        } else {
            if ($user->image) {
                Storage::disk('images_user')->delete($user->image);
            }
            $user->delete();
            return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
        }
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
