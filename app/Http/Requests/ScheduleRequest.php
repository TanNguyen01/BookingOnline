<?php

namespace App\Http\Requests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'schedules' => 'required|array',
            'schedules.*.day' => 'required|date|after_or_equal:today',

            'schedules.*.start_time' => [
                'required',
                'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/',
                'date_format:H:i:s',
            ],
            'schedules.*.end_time' => [
                'required',
                'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/',
                'date_format:H:i:s',
                'after:schedules.*.start_time',
            ],
        ];

    }

    public function messages(): array
    {

        return [
            'schedules.*.day.after_or_equal' => ' ngày chọn phải >= ngày hôm nay',
            'schedules.*.day' => 'Vui lòng chọn ngày đăng ký làm',
            'schedules.*.day.*.start_time.required' => 'giờ bắt đầu mở cửa',
            'schedules.*.day.*.start_time.date_format' => 'Chọn đúng định dạng giờ: phút: giây',
            'schedules.*.end_time' => 'giờ đóng cửa',
            'schedules.*.end_time.date_format' => 'Chọn đúng định dạng giờ: phút: giây',
            'schedules.*.end_time.after' => 'Giờ bắt đầu phải < giờ kết thúc',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'error' => $errors,
                'status_code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
