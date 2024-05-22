<?php

namespace App\Services;

use App\Models\categorie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorieService
{
    public function getAllCategorie()
    {
        return  categorie::query()->get();

    }

    public function getCategorieById($id)
    {
        $categorie =  categorie::find($id);
        if (!$categorie) {
            return response()->json(['status' => 401,
                'error' => 'Không tìm thấy danh mục'
        ]);

        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Xem danh muc thành công',
                'data' => $categorie,
            ]);
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
