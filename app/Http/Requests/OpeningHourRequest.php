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
        'store_name' => 'required|exists:store_information,name',
        'opening_hours' => 'required|array',
        'opening_hours.*.day' => 'required|date',
        'opening_hours.*.opening_time' => 'required', 'regex:/^(0[01]?[0-9]|2[0-3]):[0-5][0-9]$/',
        'opening_hours.*.closing_time' => 'required', 'regex:/^(0[01]?[0-9]|2[0-3]):[0-5][0-9]$/'|'after:opening_hours.*.opening_time',
        ];

    }
    public function messages(): array

{

    return [

        'store_name.required' => 'Vui lòng nhâp store_name',
        'store_name.exists' => 'Tên cửa hàng k đc trùng nhau',
        'opening_hours.required' => 'Vui lòng chọn ngày mở cửa',
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
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
