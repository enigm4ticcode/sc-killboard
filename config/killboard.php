<?php

return [
    'cache' => [
        'ttl' => env('CACHE_TTL_TOP_TEN', 60),
        'vehicle-ttl' => env('VEHICLE_TTL', 86400),
        'vehicles-cache-key' => env('VEHICLES_CACHE_KEY', 'all-vehicles'),
        'leaderboards-cache-key' => env('LEADERBOARDS_CACHE_KEY', 'leaderboards'),
    ],
    'leaderboards' => [
        'number-of-positions' => env('LEADERBOARDS_NUMBER_POSITIONS', 10),
    ],
];
