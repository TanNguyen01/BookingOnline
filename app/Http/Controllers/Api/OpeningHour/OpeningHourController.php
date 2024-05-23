<?php

namespace App\Http\Controllers\Api\OpeningHour;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpeningHourRequest;
use App\Traits\APIResponse;
use App\Services\OpeningService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OpeningHourController extends Controller
{
    use APIResponse;
    protected $openingService;

    public function __construct(OpeningService $openingService)
    {
        $this->openingService = $openingService;
    }

    public function index()
    {
        $openingHours = $this->openingService->getAllOpeningHours();
        return $this->responseSuccess(
            'lấy danh sách thành công',
            [
                'data' => $openingHours,
            ],
            Response::HTTP_OK
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(OpeningHourRequest $request)
    {

        $storeId = $request->input('store_information_id');
        $openingHoursData = $request->input('opening_hours');

        $result = $this->openingService->createOpeningHours($storeId, $openingHoursData);

        if ($result['status']) {
            return response()->json(['message' => $result['message']], 201);
        } else {
            return response()->json([
                'message' => $result['message'],
                'existing_days' => $result['existing_days']
            ], 401); // 400 Bad Request
        }
        return $this->responseSuccess(
            'Thêm thành công ngày giở mở cửa',
            Response::HTTP_OK
        );
    }






    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $openingHours = $this->openingService->getOpeningHour($id);
            return $this->responseSuccess(
                'lấy giờ mở cửa đóng cửa của cửa hàng theo id thành công',
                [
                    'data' => $openingHours,
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OpeningHourRequest $request,)
    {
        $storeId = $request->input('store_information_id');
        $openingHoursData = $request->input('opening_hours');
        $result = $this->openingService->updateOpeningHours($storeId, $openingHoursData);

        if ($result['status']) {
            return response()->json(['message' => $result['message']], 200);
        } else {
            return response()->json([
                'message' => $result['message'],
                'missing_days' => $result['missing_days']
            ], 400); // 400 Bad Request
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {

        $result = $this->openingService->deleteOpeningHour($id);
        return response()->json($result);
    }
}
