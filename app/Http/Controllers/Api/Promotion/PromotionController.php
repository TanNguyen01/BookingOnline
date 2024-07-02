<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\PromotionService;
use App\Models\PromotionCondition;
use Illuminate\Http\Response;
use App\Traits\APIResponse;


class PromotionController extends Controller
{
    use APIResponse;

    // Lấy danh sách tất cả các chương trình khuyến mãi
    public function index()
    {
        $promotions = Promotion::all();
        $promotions->load('conditions');
        return $this->responseSuccess(__('promotion.list'), ['data' => $promotions]);
    }

    // Lấy thông tin chi tiết của một chương trình khuyến mãi
    public function show($id)
    {

        $promotion = Promotion::with('conditions')->find($id);
        if (!$promotion) {
            return $this->responseNotFound(
                Response::HTTP_NOT_FOUND,
                __('promotion.not_found')
            );
        } else {
            return $this->responseSuccess(__('promotion.show'), ['data' => $promotion]);
        }
    }


    // Tạo mới một chương trình khuyến mãi
    public function store(StorePromotionRequest $request)
    {
        $promotionData = $request->only([
            'name', 'description', 'discount_type', 'discount_value', 'start_date', 'end_date'
        ]);

        $promotion = Promotion::create($promotionData);

        // Lưu các dịch vụ áp dụng cho chương trình khuyến mãi
        if ($request->has('service_ids')) {
            foreach ($request->service_ids as $serviceId) {
                PromotionService::create([
                    'promotion_id' => $promotion->id,
                    'service_id' => $serviceId,
                ]);
            }
        }

        // Lưu các điều kiện áp dụng cho chương trình khuyến mãi
        if ($request->has('conditions')) {
            foreach ($request->conditions as $condition) {
                PromotionCondition::create([
                    'promotion_id' => $promotion->id,
                    'condition_type' => $condition['condition_type'],
                    'condition_value' => json_encode($condition['condition_value']),
                ]);
            }
        }

        // Load lại promotion với conditions sau khi lưu thành công
        $promotion->load('conditions');

        $promotionConditions = PromotionCondition::where('promotion_id', $promotion->id)->get();

        return $this->responseCreated(__('promotion.create'), [
            'data' => $promotion,
            'promotion_conditions' => $promotionConditions
        ]);
    }


    // Cập nhật thông tin của một chương trình khuyến mãi
    public function update(StorePromotionRequest $request, $id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return $this->responseNotFound(
                Response::HTTP_NOT_FOUND,
                __('promotion.not_found')
            );
        }

            $promotionData = $request->only([
                'name', 'description', 'discount_type', 'discount_value', 'start_date', 'end_date'
            ]);

            $promotion->update($promotionData);

            // Cập nhật các dịch vụ áp dụng cho chương trình khuyến mãi
            PromotionService::where('promotion_id', $promotion->id)->delete(); // Xóa các dịch vụ cũ

            if ($request->has('service_ids')) {
                foreach ($request->service_ids as $serviceId) {
                    PromotionService::create([
                        'promotion_id' => $promotion->id,
                        'service_id' => $serviceId,
                    ]);
                }
            }

            // Cập nhật các điều kiện áp dụng cho chương trình khuyến mãi
            PromotionCondition::where('promotion_id', $promotion->id)->delete(); // Xóa các điều kiện cũ

            if ($request->has('conditions')) {
                foreach ($request->conditions as $condition) {
                    PromotionCondition::create([
                        'promotion_id' => $promotion->id,
                        'condition_type' => $condition['condition_type'],
                        'condition_value' => json_encode($condition['condition_value']),
                    ]);
                }
            }
            $promotionConditions = PromotionCondition::where('promotion_id', $promotion->id)->get();
            return $this->responseSuccess(__('promotion.update'), [
                'data' => $promotion,
                'promotion_conditions' => $promotionConditions
            ]);

    }

    // Xóa một chương trình khuyến mãi
    public function destroy($id)
    {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return $this->responseNotFound(
                Response::HTTP_NOT_FOUND,
                __('promotion.not_found')
            );
        }
        $promotion->delete();

        // Xóa các dịch vụ và điều kiện áp dụng cho chương trình khuyến mãi
        PromotionService::where('promotion_id', $id)->delete();
        PromotionCondition::where('promotion_id', $id)->delete();

        return $this->responseDeleted(null, Response::HTTP_NO_CONTENT);
    }
}
