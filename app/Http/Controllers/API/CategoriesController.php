<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Http\Resources\Categories as CategoriesResource;
use IIluminate\Support\Facades\Validator;

class CategoriesController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $categories = \App\Models\Categories::all();
         $arr = [
            'status'=>true,
            'message'=>"Danh sách danh mục",
            'data'=>\App\Http\Resources\Categories::collection($categories)
         ];

         return response()->json($arr, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $input = $request->all();
      $validator = IIluminate\Support\Facades\Validator::make($input, [
        'name'=> 'required',
      ]);

      if($validator->fails()){
        $arr = [
            'success'=>false,
            'message'=>'Lỗi kiểm tra',
            'data'=>$validator->errors()
        ];

        return response()->json($arr, 200);
      }

      $categories = \App\Models\Categories::create($input);
      $arr = [
        'status' => true,
        'message'=>'Lưu thành công danh mục',
        'data'=> new CategoriesResource($categories)

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
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\Categories $categories)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
          'name'=> 'required',
        ]);

        if($validator->fails()){
          $arr = [
              'success'=>false,
              'message'=>'Lỗi kiểm tra',
              'data'=>$validator->errors()
          ];

          return response()->json($arr, 200);
        }

        $categories->name= $input['name'];
        $categories->save();
        $arr = [
          'status' => true,
          'message'=>'Lưu thành công danh mục',
          'data'=> new \App\Http\Resources\Categories($categories)

        ];

        return response()->json($arr, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Categories $categories)
    {
        $categories->delete();
        $arr = [
            'status'=>true,
            'message'=>'Danh mục xóa thành công',
            'data'=> [],
        ];

        return response()->json($arr, 200);
    }
}
