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
        return $this->openingService->getAllOpeningHours();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(OpeningHourRequest $request)
    {

        $storeId = $request->input('store_information_id');
        $openingHoursData = $request->input('opening_hours');

        return $this->openingService->createOpeningHours($storeId, $openingHoursData);
    }






    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return $this->openingService->getOpeningHour($id);
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
        return $this->openingService->updateOpeningHours($storeId, $openingHoursData);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {

        return $this->openingService->deleteOpeningHour($id);
    }
}
