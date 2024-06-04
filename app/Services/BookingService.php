<?php

namespace App\Services;

use App\Models\booking;
use App\Traits\APIResponse;

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
