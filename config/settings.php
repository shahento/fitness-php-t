<?php

return [
    'app_url' => env('APP_URL').'/api/users',
    'image_url' => env('APP_URL') . '/images',
    'storage_url' => env('APP_URL').'/storage',

    'matrix_api_request_limit' => 25,
    'secure_mode_return_url' => "http://www.my-site.com/returnURL",

    'max_distance_limit' => 32, //KM

    'latest_slot_time_limit' => 15, // before 15 minutes of current time
    'admin_timezone' => 'America/New_York'
];
