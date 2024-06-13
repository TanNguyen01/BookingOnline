<?php

namespace App\Http\Requests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthRequest extends Handler
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
            'email' => 'required|email',
            'password' => 'required',
        ];

    }

    public function messages(): array
    {

        return [
            'email.required' => __("auth.email_required"),
            'email.email' => __("auth.email_email"),
            'password.required' => __("auth.password_required"),
        ];

    }

   // protected function failedValidation(Validator $validator)
   // {

      //  $errors = (new ValidationException($validator))->errors();
      //  throw new HttpResponseException(response()->json(
        //    [
         //       'error' => $errors,
          //      'status_code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
         //   ],
         //   JsonResponse::HTTP_UNPROCESSABLE_ENTITY
       // ));
   // }
}
