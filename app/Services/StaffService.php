<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffService
{
    public function updateProfile($user, $validatedData)
    {
        // Kiểm tra xem người dùng có quyền là nhân viên không
        if ($user->role !== 1) {
            return response()->json(['error' => 'Bạn không có quyền cập nhật hồ sơ'], 401);
        }

        // Kiểm tra xem mật khẩu hiện tại có chính xác không
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json(['error' => 'Mật khẩu hiện tại không chính xác'], 401);
        }

        // Loại bỏ trường mật khẩu hiện tại để tránh lưu vào cơ sở dữ liệu
        unset($validatedData['current_password']);

        // Nếu có mật khẩu mới, mã hóa và cập nhật vào cơ sở dữ liệu
        if (isset($validatedData['new_password'])) {
            $validatedData['password'] = bcrypt($validatedData['new_password']);
            unset($validatedData['new_password']);
        }

        // Cập nhật thông tin hồ sơ của người dùng
        $user->update($validatedData);
    }
}
