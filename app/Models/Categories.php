<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    const Active= 0;
    const Inactive=1;

    protected $fillable = [
        'name',
        'status'

    ];
}
