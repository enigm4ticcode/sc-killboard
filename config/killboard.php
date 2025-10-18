<?php

return [
    'pagination' => [
        'kills_per_page' => env('PAGINATION_KILLS_PER_PAGE', 30),
    ],
    'home_page' => [
        'most_recent_kills_days' => env('MOST_RECENT_KILLS_DAYS', 3),
    ],
    'cache' => [
        'ttl' => env('CACHE_TTL_TOP_TEN', 60),
        'vehicle-ttl' => env('VEHICLE_TTL', 86400),
        'vehicles-cache-key' => env('VEHICLES_CACHE_KEY', 'all-vehicles'),
        'leaderboards-cache-key' => env('LEADERBOARDS_CACHE_KEY', 'leaderboards'),
    ],
    'leaderboards' => [
        'timespan-days' => env('LEADERBOARDS_TIMESPAN_DAYS', 7),
        'number-of-positions' => env('LEADERBOARDS_NUMBER_POSITIONS', 5),
    ],
];
