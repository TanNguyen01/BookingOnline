<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(AuthRequest $request)
    {

        if (!$token = auth()->attempt($request->validated())) {
            return response()->json(['error' => 'Sai mật khẩu hoặc tài khoản vui lòng kiểm tra lại'], 401);
        }
        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        Session::flush();
        return response()->json([
            'status' => 'success',
        ]);

    }
}
