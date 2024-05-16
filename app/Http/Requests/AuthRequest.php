<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthRequest extends FormRequest
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
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];

    }
    public function messages(): array

{

    return [
        'email.required' => 'Vui lòng nhâp email',
        'email.email' => 'Nhập đúng định dạng email!',
        'password.required' => 'Nhập password!',
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
