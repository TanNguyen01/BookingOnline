<?php

namespace App\Http\Requests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return  [
            'name' => 'required|string|nullable',
            'address' => 'required|string|nullable',
            'phone' => 'required|string|nullable',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
        ];
    }
    public function messages(): array
    {
        return [
            'address.string' => 'phone là kiểu chuỗi',
            'address.required' => 'Vui lòng nhâp address',
            'phone.string' => 'phone là kiểu chuỗi',
            'phone.required' => 'Vui lòng nhâp phone',
            'name.required' => 'Vui lòng nhâp name',
            'name.string' => ' name là kiểu chuỗi',
            'image.mimes' => 'Hình ảnh phải có đuôi là jpg,png, jpeg',
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
