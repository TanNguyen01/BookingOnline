<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpg,png,jpeg',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'current_password' => 'required|string',
            'new_password' => 'nullable|string',
        ];

    }
    public function messages(): array

{

    return [
        'new_password.required' => 'new_password nhập new password!',
        'current_password.required' =>' nhập current_password cũ',
        'name.required' => 'Vui lòng nhâp name',
        'name.string' => ' name là kiểu chuỗi',
        'image.mimes' => 'Hình ảnh phải có đuôi là jpg,png, jpeg',
        'phone.string' => 'phone là kiểu chuỗi',
        'address.string' => 'phone là kiểu chuỗi',

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
