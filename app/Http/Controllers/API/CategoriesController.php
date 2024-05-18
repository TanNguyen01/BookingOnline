<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Categories as CategoriesResource;
use Illuminate\Validation\Rule;


class CategoriesController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $categories = Categories::all();
         $arr = [
            'status'=>true,
            'message'=>"Danh sách danh mục",
            'data'=>CategoriesResource::collection($categories)
         ];

         return response()->json($arr, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $input = $request->all();
      $validator = Validator::make($input, [
        'name'=> 'required',
        'status'=>[
            Rule::in([
                Categories::Active,
                Categories::Inactive,
            ])
        ],
      ]);

      if($validator->fails()){
        $arr = [
            'success'=>false,
            'message'=>'Lỗi kiểm tra',
            'data'=>$validator->errors()
        ];

        return response()->json($arr, 200);
      }

      $categories = Categories::create($input);
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
    public function update(Request $request, Categories $categories)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
          'name'=> 'required',
            'status'=>[
                Rule::in([
                    Categories::Active,
                    Categories::Inactive,
                ])
            ],
        ]);

        if($validator->fails()){
          $arr = [
              'success'=>false,
              'message'=>'Lỗi kiểm tra',
              'data'=>$validator->errors(),
          ];

          return response()->json($arr, 200);
        }

        $categories->name=$input['name'];
        $categories->status=$input['status'];
        $categories->save();
        $arr = [
          'status' => true,
          'message'=>'Thay đổi thành công danh mục',
          'data'=> new CategoriesResource($categories)

        ];

        return response()->json($arr, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categories $categories)
    {
        $categories->delete();
        $arr = [
            'status'=> true,
            'message'=>'Danh mục xóa thành công',
            'data'=> [],
        ];

        return response()->json($arr);
    }
}
