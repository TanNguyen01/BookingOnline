<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        if(request()->isMethod('POST')){
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
        }else{
            return [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ];
        }
    }
    public function messages(): array
    {
        if(request()->isMethod('POST')){


        return [
            'email.required' => 'Nhập e mail',
            'email.email' => ' nhập đúng định dạng e mail',
            'email.string' => ' e mail không được chứa ký tự đặc biệt',
            'password.required' => 'nhập password',
        ];
        }else{
            return [
                'email.required' => 'Nhập e mail',
                'email.email' => ' nhập đúng định dạng e mail',
                'email.string' => ' e mail không được chứa ký tự đặc biệt',
                'password.required' => 'nhập password',
            ];
        }
    }
}
