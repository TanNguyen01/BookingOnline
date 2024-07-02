<?php

namespace App\Traits;

use App\Models\PromotionService;

trait PromotionTrait
{

    public static function checkCondition($condition, $request, $services)
    {
        switch ($condition['condition_type']) {
            case 'time_range':
                $appointment_time = strtotime($request->time);
                $condition_value = json_decode($condition['condition_value'], true); // Chuyển đổi chuỗi JSON thành mảng PHP
                $start_discount_time = strtotime($condition_value['start_time']);
                $end_discount_time = strtotime($condition_value['end_time']);
                $day_of_week = date('N', strtotime($request->day));
                $valid_days = $condition_value['day_of_week'];
                return $appointment_time >= $start_discount_time &&
                    $appointment_time <= $end_discount_time &&
                    in_array($day_of_week, $valid_days);

            case 'service_count':
                $service_count = count($services);
                return $service_count >= $condition['condition_value'];
                break;

            case 'specific_service':
                $promotionId = $condition['promotion_id']; // Lấy ID của khuyến mãi từ điều kiện
                $promotionServices = PromotionService::where('promotion_id', $promotionId)->pluck('service_id')->toArray(); // Lấy danh sách các dịch vụ áp dụng cho khuyến mãi
                $conditionValue = json_decode($condition['condition_value'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON format in condition_value');
                }
                $requiredServiceIds = $conditionValue['service_ids'];

                $alreadyApplied = false;
                foreach ($services as $service) {
                    if (in_array($service['id'], $promotionServices) && in_array($service['id'], $requiredServiceIds)) {
                        $alreadyApplied = true;
                        break;
                    }
                }
                return $alreadyApplied && (count($requiredServiceIds) == count($promotionServices));

            default:
                return true;
        }
    }


    public function calculateDiscount($promotion, $original_price)
    {
        $discount_amount = 0;

        switch ($promotion->discount_type) {
            case 'percentage':
                $discount_amount = ($promotion->discount_value / 100) * $original_price;
                break;

            case 'fixed_amount':
                $discount_amount = $promotion->discount_value;
                break;

            default:
                break;
        }

        return $discount_amount;
    }
}
