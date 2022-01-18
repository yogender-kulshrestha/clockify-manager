<?php

return [

    'api_key' => env('CLOCKIFY_API_KEY', null),
    'workspace_name' => env('CLOCKIFY_WORK_SPACE', null),
    'start_time' => env('WORK_START_FROM', 6),
    'end_time' => env('WORK_END_TO', 20),
];
