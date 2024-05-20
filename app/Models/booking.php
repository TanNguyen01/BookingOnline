<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'user_id',
        'status',
        'booking_time',
    ]; //

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
