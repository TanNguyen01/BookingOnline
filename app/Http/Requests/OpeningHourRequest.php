<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OpeningHourRequest extends FormRequest
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
            'store_information_id' => 'required|exists:store_information,id',
            'opening_hours' => 'required|array',
            'opening_hours.*.day' => 'required|date|after_or_equal:today',
            'opening_hours.*.opening_time' => [
                'required',
                'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/',
                'date_format:H:i:s',
            ],
            'opening_hours.*.closing_time' => [
                'required',
                'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/',
                'date_format:H:i:s',
                'after:opening_hours.*.opening_time',
            ],
        ];
    }
    public function messages(): array

    {

        return [
            'store_name.string' => 'Tên của hàng là dạng chuổi ',
            'store_information_id.required' => 'Vui lòng nhâp id store',
            'store_information_id.exists' => 'Cửa hàng không tồn tại',
            'opening_hours.required' => 'Vui lòng chọn ngày mở cửa',
            'opening_hours.after_or_equal'=> ' ngày phải > bằng ngày hôm nay',
            'opening_hours.*.opening_time.required' => 'giờ mở cửa',
            'opening_hours.*.opening_time.date_format' => 'Chọn đúng định dạng giờ: phút: giây',
            'closing_time.required' => 'giờ đóng cửa',
            'opening_hours.*.opening_time.date_format' => 'Chọn đúng định dạng giờ: phút: giây',
            'opening_hours.*.opening_time.after' => 'Giờ đóng của phải sau giờ mở cửa',



        ];
    }
    protected function failedValidation(Validator $validator)
    {

        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'error' => $errors,
                'status_code' => 402,
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
