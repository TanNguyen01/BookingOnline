<?php
return [
    'list' => 'Xem danh sách giờ mở cửa thành công ',
    'show' => 'Chi tiết ',
    'exists' => 'Chương trình đã tồn tại',
    'create' => 'Thêm thành công Chương trình khuyến mãi',
    'update' => 'Cập nhật thành công',
    'not_found' => ' Không có chương trình ',

    // validate
    'name.required' => 'Tên chương trình khuyến mãi là bắt buộc.',
    'name.string' => 'Tên chương trình khuyến mãi phải là chuỗi ký tự.',
    'name.max' => 'Tên chương trình khuyến mãi không được vượt quá 255 ký tự.',
    'description.string' => 'Mô tả phải là chuỗi ký tự.',
    'discount_type.required' => 'Loại chiết khấu là bắt buộc.',
    'discount_type.in' => 'Loại chiết khấu phải là một trong các giá trị: percentage, fixed_amount.',
    'discount_value.required' => 'Giá trị chiết khấu là bắt buộc.',
    'discount_value.numeric' => 'Giá trị chiết khấu phải là số.',
    'discount_value.min' => 'Giá trị chiết khấu phải lớn hơn hoặc bằng 0.',
    'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
    'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
    'start_date.after' => 'Ngày bắt đầu phải sau ngày hôm nay.',
    'end_date.required' => 'Ngày kết thúc là bắt buộc.',
    'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
    'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
    'service_ids.array' => 'Danh sách dịch vụ phải là mảng.',
    'service_ids_exists' => 'Dịch vụ được chọn không hợp lệ.',
    'conditions.array' => 'Danh sách điều kiện phải là mảng.',
    'conditions_condition_type.required' => 'Loại điều kiện là bắt buộc.',
    'conditions_condition_type.string' => 'Loại điều kiện phải là chuỗi ký tự.',
    'conditions_condition_type.max' => 'Loại điều kiện không được vượt quá 255 ký tự.',
    'conditions_condition_value.required' => 'Giá trị điều kiện là bắt buộc.',

];
