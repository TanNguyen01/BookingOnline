<?php

namespace App\Http\Requests;

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
            require
            'name' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|integer|in:0,1',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'address' => 'required|string',
            'phone' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Nhập password!',
            'name.required' => 'Vui lòng nhâp name',
            'name.string' => ' Name là kiểu chuỗi',
            'image.mimes' => 'Hình ảnh phải có đuôi là jpg,png, jpeg',
            'phone.string' => 'Phone là kiểu chuỗi',
            'address.string' => 'Phone là kiểu chuỗi',

        ];
    }

  //  protected function failedValidation(Validator $validator)
   // {

      //  $errors = (new ValidationException($validator))->errors();
      //  throw new HttpResponseException(response()->json(
        //    [
          //      'error' => $errors,
          //      'status_code' => 402,
          //  ],
          //  JsonResponse::HTTP_UNPROCESSABLE_ENTITY
       // ));
  //  }
}
