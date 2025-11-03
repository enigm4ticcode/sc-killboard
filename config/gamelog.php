<?php

return [
    'actor_kill_string' => env('ACTOR_KILL_STRING', 'Actor Death'),
    'ac_match_string' => env('AC_MATCH_STRING', 'Requesting Mode Change'),
    'actor_death_pattern' => env('ACTOR_DEATH_PATTERN', "/<(?<timestamp>\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z)> \[Notice\] <Actor Death> CActor::Kill: '(?<victim>[^']+)' \[(?<victimId>\d+)\] in zone '(?<zone>[^']+)' killed by '(?<killer>[^']+)' \[(?<killerId>\d+)\] using '(?<weapon>[^']+)' \[Class (?<class>[^\]]+)\] with damage type '(?<damageType>[^']+)'/"),
    'vehicle_destruction_pattern' => env('VEHICLE_DESTRUCTION_PATTERN', "/<Vehicle Destruction> CVehicle::OnAdvanceDestroyLevel: Vehicle '(?<vehicle>[^']+)' \[(?<vehicleId>\d+)\] in zone '(?<zone>[^']+)'(?:.*)caused by '[^']+' \[\d+\](?:.*)with 'Combat' \[Team_CGP4\]\[(?<team>[^\]]+)\]/"),
    'vehicle_destruction_string' => env('VEHICLE_DESTRUCTION_STRING', 'Vehicle Destruction'),
    'valid_pvp_damage_types' => [
        'VehicleDestruction',
        'Bullet',
        'TakeDown',
    ],
    'self_destruct_weapon' => env('SELF_DESTRUCT_WEAPON', 'SelfDestruct'),
    'lines_to_read' => env('GAME_LOG_LINES_TO_READ', 4),
];
