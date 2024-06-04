<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserService
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function createUser($data)
    {
        $this->uploadImageIfExists($data);

        return User::create($data);
    }

    public function updateUser($id, $data)
    {
        $user = User::find($id);
        if ($user) {
            $this->uploadImageIfExists($data, $user);
            $user->update($data);
        }

        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            if ($user->image) {
                Storage::disk('images_user')->delete($user->image);
            }
            $user->delete();
        }

        return $user;
    }

    protected function uploadImageIfExists(&$data, $user = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('public/images/user', $imageName);

            if ($user && $user->image) {
                Storage::disk('images_user')->delete($user->image);
            }

            $data['image'] = $imageName;
        }
    }
}
