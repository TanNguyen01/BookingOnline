<?php

namespace App\Http\Controllers\Api\Client;
namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\StoreInformation;
use App\Models\User;
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
        // Kiểm tra xem cửa hàng có tồn tại không
        $store = StoreInformation::find($storeId);
        if (!$store) {
            return $this->responseBadRequest('Không tìm thấy thông tin cửa hàng.');
        }

        // Lấy tất cả người dùng có store_information_id khớp với storeId
        $users = User::where('store_information_id', $storeId)->get();

        if ($users->isEmpty()) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, 'Không tìm thấy người dùng nào.');
        }

        return $this->responseSuccess('Danh sách người dùng.', ['data' => $users]);
    }

    public function getWorkingHoursByUserAndStore(Request $request)
{
    $userId = $request->input('userId');
    // Kiểm tra và lấy thông tin người dùng
    $user = User::find($userId);
    if (!$user) {
        return $this->responseBadRequest('Không tìm thấy thông tin người dùng');
    }

    $storeId = $user->store_information_id;

    // Kiểm tra và lấy thông tin cửa hàng dựa trên store_information_id từ người dùng
    $store = StoreInformation::find($storeId);
    if (!$store) {
        return $this->responseBadRequest('Không tìm thấy thông tin cửa hàng');
    }

    // Lấy lịch làm việc của người dùng dựa trên store_information_id và user_id
    $schedules = Schedule::where('user_id', $userId)
        ->get(['day', 'start_time', 'end_time', 'created_at']);

    if ($schedules->isEmpty()) {
        return $this->responseBadRequest('Không có lịch làm việc nào');
    }

    // Chuẩn bị dữ liệu trả về, bao gồm store_information_id, store_name và danh sách lịch làm việc
    $responseData = [
        'store_information_id' => $storeId,
        'store_name' => $store->name,
        'schedules' => $schedules
    ];

    return $this->responseSuccess('Danh sách lịch làm việc.', ['data' => $responseData]);
}

}
