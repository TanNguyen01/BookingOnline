<?php

namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request)
    {
        //
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|confirmed',
            'role' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|',
            'address' => 'required|string',
            'phone' => 'required|string',

        ]);
        if($validator->fails()){
            return response()->json([
                'status' =>  401,
                'message' =>['Đăng ký thất bại',$validator->errors()->first()],
                'errors'=>$validator->errors()->toArray(),

            ]);
        }else if($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = Str::random(12) . "." . $file->getClientOriginalExtension();
            $imageDirectory = 'images/userImage';
            $file->move($imageDirectory, $imageName);
            $path = 'http://127.0.0.1:8000/'.($imageDirectory . $imageName);


                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'image' => $path,
                ]);



            return response()->json([
                'status' => 200,
                'message' => 'Đăng ký Thành Công',
            ]);





        }else{
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Đăng ký Thành Công',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
        //
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fails',
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()->toArray(),
                ]);
            }
            if (! $token = auth()->attempt($validator->validated())) {
                return response()->json(['error' => 'Sai mật khẩu hoặc tài khoản vui lòng kiểm tra lại'], 401);
            }
            $user = User::where('email',$request->email)->first();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json(['token'=> $token]);


    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
