<?php

namespace App\Http\Requests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UpdateUserRequest extends FormRequest
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
            'password' => 'required|string|min:6|max:15',
            'role' => 'required|integer|in:0,1',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'address' => 'required|string',
            'phone' => 'required|string',
            'store_id' => 'nullable|integer|exists:store_informations,id',

        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Nhập password!',
            'name.required' => 'Vui lòng nhâp name',
            'name.string' => ' Name là kiểu chuỗi',
            'role.required' =>'Chọn tư cách cho user',
            'role.in' =>'Chọn tư cách cho user chỉ  0:Admin , 1:staff',
            'image.mimes' => 'Hình ảnh phải có đuôi là jpg,png, jpeg',
            'phone.string' => 'Phone là kiểu chuỗi',
            'address.string' => 'Phone là kiểu chuỗi',
            'password.min' =>'Mật khẩu phải nhiều hơn 6 kí tự',
            'password.max' => 'Mật khẩu phải ít hơn 15 ký tự!',
            'store_id.exists' =>'Không có cửa hàng nào'

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
