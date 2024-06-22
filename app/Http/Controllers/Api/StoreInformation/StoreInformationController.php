<?php

namespace App\Http\Controllers\Api\StoreInformation;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInformationRequest;
use App\Http\Requests\UpdateStroreInformationRequest;
use App\Models\User;
use App\Services\StoreService;
use App\Traits\APIResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreInformationController extends Controller
{
    use APIResponse;

    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = $this->storeService->getAllStore();

        return $this->responseSuccess(__('store.list'), ['data' => $stores]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInformationRequest $request)
    {
        DB::beginTransaction();

        try {
            $store = $this->storeService->createStore($request->all());

            DB::commit();

            return $this->responseCreated(__('store.created'), ['data' => $store]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseServerError([__('store.error'), 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $store = $this->storeService->getStoreById($id);
        if (!$store) {
            return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
        } else {
            return $this->responseSuccess(__('store.show'), ['data' => $store]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStroreInformationRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $store = $this->storeService->updateStore($id, $request->all());
            if (!$store) {
                DB::rollBack();

                return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
            }

            DB::commit();

            return $this->responseSuccess(__('store.updated'), ['data' => $store]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseServerError([__('store.error'), 'error' => $e->getMessage()]);
        }
    }

    // update cu hang tthêm nhân viên vào cửa  hàng
    public function listStaff(){
        $users =  User::where('role' , 1 )->get();
        return $this->responseSuccess(__('user.list'), ['data' => $users]);

    }
    public function update2(UpdateStroreInformationRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $store = $this->storeService->updateStore($id, $request->all());
            if (!$store) {
                DB::rollBack();
                return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
            }

            if ($request->has('user_ids')) {
                $userIds = $request->input('user_ids');

                // Kiểm tra nếu $userIds là chuỗi, chuyển thành mảng các ID người dùng
                if (!is_array($userIds)) {
                    $userIds = explode(',', $userIds);
                }

                // Kiểm tra xem các user_id đã đăng ký lịch làm hay có booking chưa
                $hasSchedules = DB::table('schedules')
                    ->whereIn('user_id', $userIds)
                    ->where('is_valid', '1')
                    ->exists();

                $hasBookings = DB::table('bookings')
                    ->whereIn('user_id', $userIds)
                    ->exists();

                if ($hasSchedules || $hasBookings) {
                    DB::rollBack();
                    return $this->responseBadRequest('Nhân viên đang có lịch làm và booking nên không thể đổi cửa hàng');
                }

                // Cập nhật store_id cho nhân viên có role = 1 và không có lịch làm hoặc booking
                DB::table('users')
                    ->whereIn('id', $userIds)
                    ->where('role', 1)
                    ->update(['store_id' => $id]);
                $userUpdate  = DB::table('users')
                    ->whereIn('id', $userIds)
                    ->select('name', 'email', 'store_id')
                    ->get();
            }

            DB::commit();

            return $this->responseSuccess(__('store.updated'), [
                'data' => $store,
                'updated_users' => $userUpdate
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseServerError([__('store.error'), 'error' => $e->getMessage()]);
        }
    }
    // end+++++++++++++++++++++++++++++++++++++++++++

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $store = $this->storeService->deleteStore($id);
            if (!$store) {
                DB::rollBack();

                return $this->responseNotFound(Response::HTTP_NOT_FOUND, __('store.not_found'));
            }
            $store->delete();
            DB::commit();

            return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->responseServerError([__('store.error'), 'error' => $e->getMessage()]);
        }
    }
}
