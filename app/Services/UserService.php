<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{
    public function getAllUsers()
    {
        return  User::query()->get();

    }

    public function getUserById($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return response()->json(['status' => 401,
                'error' => 'Không tìm thấy người dùng'
        ]);
        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Xem  người dùng thành công',
                'data' => $user,
            ]);
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
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return $user;
    }

    protected function uploadImageIfExists(&$data, $user = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12) . "." . $data['image']->getClientOriginalExtension();
            $data['image']->storeAs('public', $imageName);

            if ($user && $user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $data['image'] = $imageName;
        }
    }
}
