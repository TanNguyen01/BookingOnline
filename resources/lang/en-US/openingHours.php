<?php

return [

    'list' => 'See list of successful opening hours ',
    'show' => "Store opening hours.",
    'exists' => 'Opening hours is already exists',
    'create' => 'Opening hours is added successfully',
    'update' => 'Opening hours is updated successfully',

    'not_found' => 'No opening hours available, please wait.',

    /**validate */

    'store_information_id_required' => 'store is required',
    'opening_hours_day_required' => 'day of opening hours is required',
    'opening_hours_day_after_or_equal' => ' The selected date must be greater than or equal to today is date',
    'opening_hours_opening_time_required' => 'opening hours is required',
    'opening_hours_opening_time_date_format' => 'Opening hours must be type hour: minute : second: ',
    'closing_time_required' => 'closing_time is required',
    'opening_hours_opening_time_after' => 'closing time must be after opening time',

    'opening_hours_start_in_time'=>'The start time must be within the opening and closing hours.'
];
