<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInformation extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable  =[
        'name',
        'image',
        'address',
        'phone',
    ];
    public $timestamps = false;

    // vì mỗi cửa hàng trong bảng Store_Information có thể có nhiều bản ghi trong bảng opening_hours,
    public function openingHours()
    {
        return $this->hasMany(OpeningHour::class, 'store_information_id');
    }


    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

}
