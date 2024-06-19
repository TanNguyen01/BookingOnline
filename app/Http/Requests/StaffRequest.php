<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StaffRequest extends FormRequest
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
            'current_password' => 'required|string',
            'name' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|max:15',
        ];

    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Nhập current_password cũ',
            'new_password.min' => 'Mật khẩu mới phải nhiều hơn 6 kí tự!',
            'new_password.max' => 'Mật khẩu mới phải ít hơn 15 ký tự!',
            'name.required' => 'Vui lòng nhâp name',
            'name.string' => ' Name là kiểu chuỗi',
            'image.mimes' => 'Hình ảnh phải có đuôi là jpg,png, jpeg',
            'phone.string' => 'Phone là kiểu chuỗi',
            'address.string' => 'Phone là kiểu chuỗi',
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
