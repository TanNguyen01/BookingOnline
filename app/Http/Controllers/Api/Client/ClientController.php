<?php

namespace App\Http\Controllers\APi\Client;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\StoreInformation;
use App\Models\User;
use App\Services\ServiceService;
use App\Services\StoreService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    use APIResponse;
    protected $storeService;
    protected $serviceService;


    public function __construct(StoreService $storeService ,ServiceService $serviceService)
    {
        $this->storeService = $storeService;
        $this->serviceService = $serviceService;
    }

    public function listStore()
    {
        $stores = $this->storeService->getAllStore();

        return $this->responseSuccess(__('store.list'), ['data' => $stores]);
    }

    public function listService()
    {
        $services = $this->serviceService->getAllService();
        return $this->responseSuccess(__('service.list'), ['data' => $services]);
    }
    public function getUsersByStoreInformation(Request $request)
    {

        $storeId = $request->input('storeId');

        $store = StoreInformation::find($storeId);
        if (!$store) {
            return $this->responseBadRequest('Không tìm thấy thông tin cửa hàng');
        }
        $storeInformation = StoreInformation::with(['schedules.user'])
            ->where('id', $storeId)
            ->first();

        if (!$storeInformation) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('storeId.not_found'));
        }

        $users = $storeInformation->schedules->map(function ($schedule) {
            return $schedule->user;
        })->unique('id');

        return $this->responseSuccess(__('users.list'), ['data' => $users]);
    }

    public function getWorkingHoursByUserAndStore(Request $request)
    {
        $storeId = $request->input('storeId');
        $userId = $request->input('userId');
        $store = StoreInformation::find($storeId);
        if (!$store) {
            return $this->responseBadRequest('Không tìm thấy thông tin cửa hàng');
        }

        // Kiểm tra sự tồn tại của $userId trong bảng User
        $user = User::find($userId);
        if (!$user) {
            return $this->responseBadRequest('Không tìm thấy thông tin người dùng');
        }

        $schedules = Schedule::where('store_information_id', $storeId)
            ->where('user_id', $userId)
            ->get(['day','start_time', 'end_time', 'created_at']);

        if ($schedules->isEmpty()) {
            return $this->responseBadRequest([Response::HTTP_BAD_REQUEST, 'Không có lịch làm nào']);
        }

        return $this->responseSuccess(__('schedules.list'), ['data' => $schedules]);
    }

}
