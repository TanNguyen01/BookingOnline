<?php

namespace App\Http\Controllers\Api\OpeningHour;

use App\Http\Controllers\Controller;
use App\Models\OpeningHour;
use App\Models\StoreInformation;
use App\Services\OpeningService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|exists:store_information,name',
            'day' => 'required|date',
            'opening_time' => 'required|date_format:H:i:s',
            'closing_time' => 'required|date_format:H:i:s|after:opening_time',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' =>  401,
                'message' => ['Thêm giờ mở cửa thất bại', $validator->errors()->first()],
                'errors' => $validator->errors()->toArray(),

            ]);
        } else {
            $storeName = $request->store_name;
            $data = $request->only(['day', 'opening_time', 'closing_time']);

            $openingHour = $this->openingService->createOpeningHour($data, $storeName);

            return response()->json([
                'opening_hour' => $openingHour,
                'status' => 201,
                'message' => 'Thêm giờ mở cửa thành công'
            ]);
        }
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
    public function update(Request $request, $storeName)
    {
        //
        $validator = Validator::make(
            $request->all(),
            [
                'store_name' => 'required|exists:store_information,name',
                'day' => 'required|date',
                'opening_time' => 'required|date_format:H:i:s',
                'closing_time' => 'required|date_format:H:i:s|after:opening_time',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' =>  401,
                'message' => ['cập nhật mở cửa thất bại', $validator->errors()->first()],
                'errors' => $validator->errors()->toArray(),

            ]);
        }
        $result = $this->openingService->updateOpeningHours($storeName, $request->only(['day', 'opening_time', 'closing_time']));

        // Trả về kết quả từ service

        // Trả về thông báo thành công
        return response()->json([$result, 'message' => 'Thông tin mở cửa đã được cập nhật'], 200);
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
