<?php

namespace App\Http\Controllers\Api\staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Services\StaffService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    public function updateProfile(StaffRequest $request)
    {
        $user = Auth::user();
        $validatedData = $request->validated();
        return $this->staffService->updateProfile($user, $validatedData);
        return response()->json(['message' => 'Hồ sơ đã được cập nhật thành công']);
    }
}
