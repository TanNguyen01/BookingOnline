<?php

namespace App\Services;

use App\Models\categorie;
use App\Traits\APIResponse;
use Illuminate\Http\Response;

class CategorieService
{
    use APIResponse;

    public function getAllCategorie()
    {
        return  categorie::query()->get();

    }

    public function getCategorieById($id)
    {
        $categorie =  categorie::find($id);
        if (!$categorie) {
            return $this ->responseBadRequest(
                'Không tìm thấy dịch vụ',
                Response::HTTP_BAD_REQUEST);

        }else{
            return $this->responseSuccess(
                'Xem dịch vụ thành công',
                [
                    'data' => $categorie,

                ],
                Response::HTTP_OK
            );
        }
    }

    public function createCategorie($data)
    {


        return categorie::create($data);
    }

    public function updateCategorie($id, $data)
    {
        $categorie = categorie::findOrFail($id);

        $categorie->update($data);

        return $categorie;
    }

    public function deleteCategorie($id)
    {
        $categorie = categorie::findOrFail($id);
        $categorie->delete();
        return $categorie;
    }
}
