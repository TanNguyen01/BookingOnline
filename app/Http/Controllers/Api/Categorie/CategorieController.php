<?php

namespace App\Http\Controllers\Api\Categorie;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategorieRequest;
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
        $categorie = $this->categorieService->getAllCategorie();

        return $this->responseSuccess(
           __('message.category_list'),
            [
                'data' => $categorie,
            ]
        );

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CategorieRequest $request)
    {
        $data = $request->all();
        $categorie = $this->categorieService->createCategorie($data);

        return $this->responseCreated(
            __('message.category_created'),

            [
                'data' => $categorie,

            ],
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categorie = $this->categorieService->getCategorieById($id);
        if (!$categorie) {
            return $this->responseNotFound(
                __('message.category_not_found'),

                Response::HTTP_NOT_FOUND
            );
        } else {
            return $this->responseSuccess(
                __('message.category_list'),

                [
                    'data' => $categorie,

                ],
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategorieRequest $request, string $id)
    {
        $data = $request->all();
        $categorie = $this->categorieService->updateCategorie($id, $data);
        if (!$categorie) {
            return $this->responseNotFound(
                __('message.category_not_found'),
                Response::HTTP_NOT_FOUND
            );
        } else {
            $categorie->update($data);
            return $this->responseSuccess(
                __('message.category_updated'),
                [
                    'data' => $categorie,

                ],
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categorie =  $this->categorieService->deleteCategorie($id);
        if (!$categorie) {
            return $this->responseNotFound(
                __('message.category_not_found'),

                Response::HTTP_NOT_FOUND
            );
        } else {

            $categorie->delete();
            return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
        }
    }
}
