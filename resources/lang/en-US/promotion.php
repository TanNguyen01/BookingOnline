<?php
return [
    'list' => 'See list of successful opening hours ',
    'show' => 'Store opening hours.',
    'exists' => 'Promotion is already exists',
    'create' => 'Promotion is added successfully',
    'update' => 'Promotion  is updated successfully',
    'not_found' => 'Promotion is not found',

    // validate
    'name.required' => 'Promotion name is required.',
    'name.string' => 'Promotion name must be a string.',
    'name.max' => 'Promotion name must not exceed 255 characters.',
    'description.string' => 'Description must be a string.',
    'discount_type.required' => 'Discount type is required.',
    'discount_type.in' => 'Discount type must be one of the following values: percentage, fixed_amount.',
    'discount_value.required' => 'Discount value is required.',
    'discount_value.numeric' => 'Discount value must be a number.',
    'discount_value.min' => 'Discount value must be at least 0.',
    'start_date.required' => 'Start date is required.',
    'start_date.date' => 'Start date must be a valid date.',
    'start_date.after' => 'Start date must be after today.',
    'end_date.required' => 'End date is required.',
    'end_date.date' => 'End date must be a valid date.',
    'end_date.after' => 'End date must be after the start date.',
    'service_ids.array' => 'Service IDs must be an array.',
    'service_ids_exists' => 'The selected service is invalid.',
    'conditions.array' => 'Conditions must be an array.',
    'conditions_condition_type.required' => 'Condition type is required.',
    'conditions_condition_type.string' => 'Condition type must be a string.',
    'conditions_condition_type.max' => 'Condition type must not exceed 255 characters.',
    'conditions_condition_value.required' => 'Condition value is required.',

];
