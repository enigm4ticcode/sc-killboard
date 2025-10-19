<?php

return [
    'actor_kill_string' => env('ACTOR_KILL_STRING', 'Actor Death'),
    'vehicle_destruction_string' => env('VEHICLE_DESTRUCTION_STRING', 'Vehicle Destruction'),
    'valid_pvp_damage_types' => [
        'VehicleDestruction',
        'Bullet',
    ],
    'iso_timestamp_pattern' => env('ISO_TIMESTAMP_PATTERN', '/\<(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2}))\>/'),
    'arena_commander_zone_prefixes' => [
        'ooc_',
    ],
    'self_destruct_weapon' => env('SELF_DESTRUCT_WEAPON', 'SelfDestruct'),
    'lines_to_read' => env('GAME_LOG_LINES_TO_READ', 4),
];
