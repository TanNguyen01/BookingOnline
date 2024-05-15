<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Http\Resources\Store as StoreResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;



class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
               $store = Store::all();
               $arr = [
                 'status'=> true,
                 'message'=> 'Cửa hàng',
                 'data'=> StoreResource::collection($store),
               ];

               return response()->json($arr, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|',
        ]);

        if ($validator->fails()) {
            $arr = [
                'success' => false,
                'message' => 'Lỗi kiểm tra',
                'data' => $validator->errors()
            ];

            return response()->json($arr, 200);
        } else if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = Str::random(12) .
                "." . $file->getClientOriginalExtension();
            $imageDirectory = 'images/userImage';
            $file->move($imageDirectory, $imageName);
            $path = 'http://127.0.0.1:8000/' . ($imageDirectory . $imageName);

            $store = \App\Models\Store::create($input);

            $arr = [
                'status' => true,
                'message' => 'Lưu thành công cửa hàng',
                'data' => new StoreResource($store)

            ];

            return response()->json($arr, 201);
        }
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

