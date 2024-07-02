<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StorePromotionRequest extends FormRequest
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'discount_type' => 'required|string|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'start_date' => 'required|date|after:today',
                'end_date' => 'required|date|after:start_date',
                'service_ids' => 'nullable|array',
                'service_ids.*' => 'exists:services,id',
                'conditions' => 'nullable|array',
                'conditions.*.condition_type' => 'required|string|max:255',
                'conditions.*.condition_value' => 'required',
            ];

    }
    public function messages()
    {
        return [
            'name.required' => __('promotion.name.required'),
            'name.string' => __('promotion.name.string'),
            'name.max' => __('promotion.name.max'),
            'description.string' => __('promotion.description.string'),
            'discount_type.required' => __('promotion.discount_type.required'),
            'discount_type.in' => __('promotion.discount_type.in'),
            'discount_value.required' => __('promotion.discount_value.required'),
            'discount_value.numeric' => __('promotion.discount_value.numeric'),
            'discount_value.min' => __('promotion.discount_value.min'),
            'start_date.required' => __('promotion.start_date.required'),
            'start_date.date' => __('promotion.start_date.date'),
            'start_date.after' => __('promotion.start_date.after'),
            'end_date.required' => __('promotion.end_date.required'),
            'end_date.date' => __('promotion.end_date.date'),
            'end_date.after' => __('promotion.end_date.after'),
            'service_ids.array' => __('promotion.service_ids.array'),
            'service_ids.*.exists' => __('promotion.service_ids.*.exists'),
            'conditions.array' => __('promotion.conditions.array'),
            'conditions.*.condition_type.required' => __('promotion.conditions_condition_type.required'),
            'conditions.*.condition_type.string' => __('promotion.conditions_condition_type.string'),
            'conditions.*.condition_type.max' => __('promotion.conditions_condition_type.max'),
            'conditions.*.condition_value.required' => __('promotion.conditions_condition_value.required'),
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
