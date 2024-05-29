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
        return categorie::query()->get();

    }

    public function getCategorieById($id)
    {
      return categorie::find($id);

    }

    public function createCategorie($data)
    {

        return categorie::create($data);

    }

    public function updateCategorie($id, $data)
    {
        return categorie::find($id);

    }

    public function deleteCategorie($id)
    {
       return categorie::find($id);

    }
}
