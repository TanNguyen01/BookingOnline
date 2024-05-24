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
        return $this->categorieService->getAllCategorie();

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CategorieRequest $request)
    {
        $data = $request->all();
        return $this->categorieService->createCategorie($data);

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
        return $this->categorieService->updateCategorie($id, $data);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       return $this->categorieService->deleteCategorie($id);
    }
}
