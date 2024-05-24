<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Traits\APIResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    use APIResponse;

    /**
     * Display a listing of the resource.
     */
    public function login(AuthRequest $request)
    {

        if (!$token = auth()->attempt($request->validated())) {
            return $this ->responseBadRequest('đăng nhập thất bại vui long kiểm tra lại',
             Response::HTTP_BAD_REQUEST);
        }
        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth-token')->plainTextToken;
        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        Session::flush();
        return $this->responseSuccess(
            'Đăng xuất thành công',
        );
    }
}
