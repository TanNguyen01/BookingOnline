<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'day', 'time', 'status',
    ];

    // public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_service');
    }
    public function bases()
    {
        return $this->hasMany(Base::class, 'booking_id', 'id');
    }
}
