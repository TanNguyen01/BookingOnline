<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class userRequest extends FormRequest
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
        if (request()->isMethod('post')) {
            return [
                'email' => 'required|string|email|unique:users',
                'name' => 'nullable|string',
                'password' => 'required|string|',
                'role' => 'nullable|integer',
                'image' => 'nullable|image|mimes:jpg,png,jpeg',
                'address' => 'nullable|string',
                'phone' => 'nullable|string',

            ];
        } else {
            return [
                'email' => 'nullable|string|email|unique:users',
                'name' => 'nullable|string',
                'password' => 'nullable|string',
                'role' => 'nullable|integer|in:0,1',
                'image' => 'nullable|image|mimes:jpg,png,jpeg',
                'address' => 'nullable|string',
                'phone' => 'nullable|string',

            ];
        }

    }

    public function messages(): array
    {

        return [
            'email.unique' => __('user.email_unique'),
            'email.required' => __('user.email_required'),
            'email.email' => __('user.email_email'),
            'password.required' => __('user.password_required'),
            'password.confirmed' => __('user.password_confirmed'),
            'name.required' => __('user.name_required'),
            'name.string' => __('user.name_string'),
            'image.mimes' => __('user.image_mimes'),
            'phone.string' => __('user.phone_string'),
            'address.string' => __('user.address_string'),

        ];

    }

    protected function failedValidation(Validator $validator)
    {

        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'error' => $errors,
                'status_code' => 402,

                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ]
        ));
    }
}
