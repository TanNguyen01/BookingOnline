<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateStroreInformationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $storeId = $this->route('store');
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('store_information')->ignore($storeId),
            ],
            'address' => 'required|string|nullable',
            'phone' => 'required|string|nullable',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'address.string' => 'Address phải là kiểu chuỗi.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'phone.string' => 'Phone phải là kiểu chuỗi.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'name.required' => 'Vui lòng nhập tên.',
            'name.string' => 'Tên phải là kiểu chuỗi.',
            'name.unique' => 'Tên đã tồn tại trong hệ thống.',
            'image.mimes' => 'Hình ảnh phải có đuôi là jpg, png, jpeg.',
            'latitude.string' => 'Vĩ độ phải là chuỗi ký tự.',
            'longitude.string' => 'Kinh độ phải là chuỗi ký tự.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'errors' => $errors,
                'status_code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
