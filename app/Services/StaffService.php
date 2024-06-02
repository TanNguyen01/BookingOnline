<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\OpeningHour;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaffService
{
    public function staffService(){
       return Auth::user();
    }
    public function uploadImageIfExists(&$data, $user = null)
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = Str::random(12) . "." . $data['image']->getClientOriginalExtension();
            $data['image']->storeAs('', $imageName, 'images_user');

            if ($user && $user->image) {
                Storage::disk('images_user')->delete($user->image);
            }

            $data['image'] = $imageName;
        }
    }
}
