<?php

return [

    "list"=>"xem danh sach booking thanh cong",
    "create"=> "booking thanh cong",
    "show"=>"xem chi tiet booking thanh cong",
    "update"=>"cap nhat trang thai booking thanh cong",
    "not_found"=>"khong tim thay booking",
    "error"=>"Đã xảy ra lỗi. Vui lòng thử lại sau",

    "error_create"=> "Da xay ra loi khi tao booking",
    "error_update"=> "Da xay ra loi khi cap nhat trang thai booking",





    /** Validate */
    'user_id.required' => 'Nhập user  ',
    'user_id.exists' => 'User không tồn tại',
    'day.required' => 'Chọn ngày',
    'day.after_or_equal' => 'Chọn ngày  phải > = ngày hôm nay',
    'time.required' => 'Chọn giờ!',
    'service_id.required' => 'Chọn dịch vụ  ',
    'service_id.exists' => 'Dịch vụ không tồn tại',
];
