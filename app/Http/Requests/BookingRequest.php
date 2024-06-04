<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BookingRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'day' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'service_id' => 'required|exists:services,id',
        ];
    }

    public function messages(): array
    {

        return [
            'user_id.required' => 'Nhập user id ',
            'user_id.exists' => 'User không tồn tại',
            'day.required' => 'Chọn ngày',
            'day.after_or_equal' => 'Chọn ngày  phải > = ngày hôm nay',
            'time.required' => 'Chọn giờ!',
            'service_id.required' => 'Chọn dịch vụ id ',
            'service_id.exists' => 'Dịch vụ không tồn tại',

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
