<?php

namespace App\Http\Controllers\Api\OpeningHour;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpeningHourRequest;
use App\Models\OpeningHour;
use App\Models\StoreInformation;
use App\Services\OpeningService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class OpeningHourController extends Controller
{
    protected $openingService;

    public function __construct(OpeningService $openingService)
    {
        $this->openingService = $openingService;
    }

    public function index()
    {
        $openingHours = $this->openingService->getAllOpeningHours();

        return response()->json([
            'opening_hours' => $openingHours,
            'status' => 200,
            'message' => 'Lấy danh sách giờ mở cửa thành công'
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(OpeningHourRequest $request)
    {
        //

        $storeName = $request->input('store_name');
        $openingHoursData = $request->input('opening_hours');
        $this->openingService->createOpeningHours($storeName, $openingHoursData);

        return response()->json(['message' => 'Giờ làm của cửa hàng đã được thêm.'], 200);

    }



    /**
     * Display the specified resource.
     */
    public function show($storeName)
    {
        try {
            $openingHours = $this->openingService->getOpeningHour($storeName);
            return response()->json([
                'store_name' => $storeName,
                'opening_hours' => $openingHours
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OpeningHourRequest $request, $storeName)
    {
        //
        $storeName = $request->input('store_name');
    $openingHoursData = $request->input('opening_hours');

    $this->openingService->updateOpeningHours($storeName, $openingHoursData);

    return response()->json(['message' => 'Thông tin giờ làm của cửa hàng đã được cập nhật.'], 200);

    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $storeName)
    {

        $result = $this->openingService->deleteOpeningHour($storeName);

        // Trả về kết quả từ service
        return response()->json($result);
    }
}
