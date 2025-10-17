<?php

return [
    'actor_kill_string' => env('ACTOR_KILL_STRING', 'Actor Death'),
    'npc_kill_string' => env('NPC_KILL_STRING', 'NPC'),
    'iso_timestamp_pattern' => env('ISO_TIMESTAMP_PATTERN', '/\<(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2}))\>/'),
];
