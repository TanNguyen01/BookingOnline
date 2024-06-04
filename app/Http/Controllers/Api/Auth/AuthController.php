<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    use APIResponse;

    /**
     * Display a listing of the resource.
     */
    public function login(AuthRequest $request)
    {

        if (! $token = auth()->attempt($request->validated())) {
            return $this->responseBadRequest(null, 'đăng nhập thất bại vui long kiểm tra lại');
        }
        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->responseSuccess('Đăng nhập thành công', [
            'data' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        Session::flush();

        return $this->responseSuccess(
            'Đăng xuất thành công',
        );
    }
}
