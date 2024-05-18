<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenHour extends Model
{
    use HasFactory;
    protected $table = 'opening_hours';
    protected $fillable = ['store_information_id', 'day', 'opening_time', 'closing_time'];
}
