<?php

return [
    'actor_kill_string' => env('ACTOR_KILL_STRING', 'Actor Death'),
    'iso_timestamp_pattern' => env('ISO_TIMESTAMP_PATTERN', '/\<(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2}))\>/'),
    'arena_commander_zone_prefixes' => [
        'ooc_',
    ],
];
