<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    use HasFactory;
    protected $fillable= [
        'booking_id',
        'date',
        'phone',
        'status',
        'note',
        'email',
        'name',
        'staff_name',
        'store_name',
    ];


    public function booking()
    {
        return $this->belongsTo(booking::class, 'booking_id', 'id');
    }
}



