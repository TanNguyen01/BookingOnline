<?php

namespace App\Services;

use App\Models\booking;
use App\Traits\APIResponse;
use Illuminate\Http\Response;

class BookingService
{
    use APIResponse;

    public function getAllBooking()
    {
        return booking::query()->get();

    }

    public function getBookingById($id)
    {
      return booking::find($id);

    }



}
