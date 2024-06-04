<?php

namespace App\Http\Controllers\Api\OpeningHour;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpeningHourRequest;
use App\Services\OpeningService;
use App\Traits\APIResponse;
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

        return response()->json(['data' => $openingHours], Response::HTTP_OK);
    }

    public function show($storeid)
    {
        $openingHours = $this->openingService->getOpeningHour($storeid);

        return response()->json(['data' => $openingHours], Response::HTTP_OK);
    }

    public function store(OpeningHourRequest $request)
    {
        $storeId = $request->input('store_information_id');
        $openingHoursData = $request->input('opening_hours');
        $opening = $this->openingService->createOpeningHours($storeId, $openingHoursData);
        if (isset($opening['error'])) {
            return response()->json(['error' => $opening['error']], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['Them thanh cong' => $opening], Response::HTTP_CREATED);

    }

    public function update(OpeningHourRequest $request)
    {

        $storeId = $request->input('store_information_id');
        $openingHoursData = $request->input('opening_hours');

        $result = $this->openingService->updateOpeningHours($storeId, $openingHoursData);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], Response::HTTP_BAD_REQUEST);
        } else {
            return $this->responseSuccess('Cập nhật thành công', ['data' => $result]);
        }

    }

    public function destroy($id)
    {
        $deleted = $this->openingService->deleteOpeningHour($id);
        if (! $deleted) {
            return response()->json(['message' => 'Không có ngày nào để xóa'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
