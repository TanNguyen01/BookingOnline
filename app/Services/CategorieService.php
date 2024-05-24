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
        $categorie = categorie::query()->get();
        return $this->responseSuccess(
            'Xem dịch vụ thành công',
            [
                'data' => $categorie,
            ]
        );
    }

    public function getCategorieById($id)
    {
        $categorie =  categorie::find($id);
        if (!$categorie) {
            return $this->responseNotFound(
                'Không tìm thấy dịch vụ',
                Response::HTTP_NOT_FOUND
            );
        } else {
            return $this->responseSuccess(
                'Xem dịch vụ thành công',
                [
                    'data' => $categorie,

                ],
            );
        }
    }

    public function createCategorie($data)
    {

        $categorie = categorie::create($data);
        return $this->responseCreated(
            'Thêm Danh mục thành công',
            [
                'data' => $categorie,

            ],
        );
    }

    public function updateCategorie($id, $data)
    {
        $categorie = categorie::find($id);
        if (!$categorie) {
            return $this->responseNotFound(
                'Không tìm thấy dịch vụ',
                Response::HTTP_NOT_FOUND
            );
        } else {
            $categorie->update($data);
            return $this->responseSuccess(
                'cập nhật thành công',
                [
                    'data' => $categorie,

                ],
            );
        }
    }

    public function deleteCategorie($id)
    {
        $categorie = categorie::find($id);
        if (!$categorie) {
            return $this->responseNotFound(
                'Không tìm danh mục',
                Response::HTTP_NOT_FOUND
            );
        } else {

            $categorie->delete();
            return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
        }
    }
}
