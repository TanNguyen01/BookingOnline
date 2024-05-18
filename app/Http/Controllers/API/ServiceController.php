<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\Service as ServiceResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = Service::all();
        $arr = [
            'status'=>true,
            'message'=>"Danh sách dịch vụ",
            'data'=>ServiceResource::collection($service)
        ];

        return response()->json($arr, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Categories::query()
             ->where('status', true)
             ->latest()
             ->pluck('name','id')
             ->all();

        response()->json($categories, 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name'=> 'required|string|max:225',
            'categories_id'=>[
                'required',
                Rule::exists('categories','id')->where('status',true),
            ],
            'describe'=>'required',
            'price'=>'required',
        ]);

        if($validator->fails()){
            $arr = [
                'success'=>false,
                'message'=>'Lỗi kiểm tra',
                'data'=>$validator->errors()
            ];

            return response()->json($arr, 200);
        }

        $service = Service::create($input);
        $arr = [
            'status' => true,
            'message'=>'Lưu thành công danh mục',
            'data'=> new ServiceResource($service)

        ];

        return response()->json($arr, 201);
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

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name'=> 'required',
            'categories_id'=>[
                'required',
                Rule::exists('categories','id')->where('status',true),
            ],
            'describe'=>'required',
            'price'=>'required',
        ]);

        if($validator->fails()){
            $arr = [
                'success'=>false,
                'message'=>'Lỗi kiểm tra',
                'data'=>$validator->errors()
            ];

            return response()->json($arr, 200);
        };
        $service->name=$input['name'];
        $service->categories_id=$input['categories_id'];
        $service->describe=$input['describe'];
        $service->price=$input['price'];
        $service->save();
        $arr = [
            'status' => true,
            'message'=>'Đã thay đổi dịch vụ',
            'data'=> new ServiceResource($service)

        ];

        return response()->json($arr, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        $arr = [
            'status'=> true,
            'message'=>'Dịch vụ đã được xóa',
            'data'=> [],
        ];

        return response()->json($arr, 200);
    }
}
