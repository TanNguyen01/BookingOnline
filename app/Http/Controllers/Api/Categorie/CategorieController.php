<?php

namespace App\Http\Controllers\Api\Categorie;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategorieRequest;
use App\Models\categories;
use App\Services\CategorieService;
use App\Traits\APIResponse;
use Illuminate\Http\Response;

class CategorieController extends Controller
{
    use APIResponse;
    protected $categorieService;
    public function __construct(CategorieService $categorieService)
    {
        $this->categorieService = $categorieService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->categorieService->getAllCategorie();
        return $this->responseSuccess(
            'Lấy danh sách thành công',
            [
                'data' => $users,

            ],
            Response::HTTP_OK
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CategorieRequest $request)
    {
        $data = $request->all();
        $categorie = $this->categorieService->createCategorie($data);
        return $this->responseSuccess(
            'Thêm Danh mục thành công',
            [
                'data' => $categorie,

            ],
            Response::HTTP_OK
        );
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
        return $this->responseSuccess(
            'Cập nhật thành công',
            [
                'data' => $categorie,

            ],
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->categorieService->deleteCategorie($id);
        return $this->responseSuccess(
            'Xóa thành công',
            Response::HTTP_OK
        );
    }
}
