<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OpenHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OpenHourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $list_open_hour = OpenHour::all();
        return response()->json(['data' => $list_open_hour], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $validated = Validator::make($request->all(), [
            'store_information_id' => ['required'],
            'day' => ['required'],
            'opening_time' => ['required'],
            'closing_time' => ['required']
        ]);

        if ($validated->fails()) {

            return response()->json(['message' => 'Kiểm trả lại các trường nhập'], 500);
        }

        $data = $request->all();

        $inertTable = OpenHour::create($data);

        if ($inertTable->id) {
            return response()->json(['message' => 'Thêm giờ hoạt động thành công'], 200);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $id_open_hour = $id;

        if (intval($id_open_hour) > 0) {

            $open_hour = OpenHour::find($id_open_hour);
            if ($open_hour) {
                return response()->json(['data' => $open_hour], 200);
            }
            return response()->json(['message' => 'Not found OpenHour'], 404);
        }
        return response()->json(['message' => 'Not found OpenHour'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        $validated = Validator::make($request->all(), [
            'store_information_id' => ['required'],
            'day' => ['required'],
            'opening_time' => ['required'],
            'closing_time' => ['required']
        ]);

        if ($validated->fails()) {

            return response()->json(['message' => 'Kiểm trả lại các trường nhập'], 500);
        }

        $data = $request->all();

        $updateOpenHour = OpenHour::where('id', $id)->update($data);

        if ($updateOpenHour) {
            return response()->json(['message' => 'Cập nhật giờ hoạt động thành công'], 200);
            
        }
        return response()->json(['message' => 'Lỗi cập nhật giờ hoạt động thành công'], 500);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $id_open_hour = $id;

        if(intval($id) > 0) {
            $open_hour = OpenHour::find($id_open_hour);
            $open_hour->delete();
            return response()->json(['message' => 'Xóa thành công!'], 200);
        }
        return response()->json(['message' => 'Lỗi xóa giờ hoạt động!'], 500);
    }
}
