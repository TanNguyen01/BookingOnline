<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'describe',
        'name',
        'categorie_id'

    ];

    public function category()
    {
        return $this->belongsTo(categorie::class);
    }
}
