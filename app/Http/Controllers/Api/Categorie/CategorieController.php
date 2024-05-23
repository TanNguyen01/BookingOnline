<?php

namespace App\Http\Controllers\Api\Categorie;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategorieRequest;
use App\Models\categories;
use App\Services\CategorieService;
use Illuminate\Http\Request;

class CategorieController extends Controller
{

    protected $categorieService;
    public function __construct(CategorieService $categorieService){
        $this->categorieService = $categorieService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->categorieService->getAllCategorie();

        return response()->json([
            'status' => 201,
            'message' => 'Lấy danh sách Danh mục thành công',
            'data' => $users,
        ], 201);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CategorieRequest $request)
    {
        $data = $request->all();
        $categorie = $this->categorieService->createCategorie($data);
        return response()->json([
            'status' => 201,
            'message' => 'Thêm Danh mục thành công',
            'data' => $categorie,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       return $this->categorieService->getCategorieById($id);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategorieRequest $request, string $id)
    {
        $data = $request->all();
        $categorie = $this->categorieService->updateCategorie($id, $data);
        return response()->json([
            'status' => 201,
            'message' => 'Cập nhật thành công',
            'data' => $categorie,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->categorieService->deleteCategorie($id);

        return response()->json([
            'status' => 201,
            'message' => 'Xóa thành công',
        ]);
    }
}
