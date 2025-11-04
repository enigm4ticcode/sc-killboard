<?php

return [
    'actor_kill_string' => env('ACTOR_KILL_STRING', 'Actor Death'),
    'ac_match_strings' => ['Requesting Mode Change', 'Join Lobby'],
    'vehicle_destruction_string' => env('VEHICLE_DESTRUCTION_STRING', 'Vehicle Destruction'),
    'valid_pvp_damage_types' => [
        'VehicleDestruction',
        'Bullet',
        'TakeDown',
    ],
    'self_destruct_weapon' => env('SELF_DESTRUCT_WEAPON', 'SelfDestruct'),
    'lines_to_read' => env('GAME_LOG_LINES_TO_READ', 4),
    'kill_time_tolerance_seconds' => env('KILL_TIME_TOLERANCE_SECONDS', 3),
    'weapons' => [
        'fps_weapon_patterns' => [
            'toy',
            'multitool',
            'repair',
            'cutter',
            'tractor',
            'carryable',
            'shotgun',
            'sniper',
            'rifle',
            'smg',
            'pistol',
            'lmg',
            'volt',
        ],
        'fps_weapon_types' => [
            'energy',
            'ballistic',
            'melee',
        ],
    ],
    'manufacturers' => [
        'fps_manufacturers' => [
            'gmni',
            'hdgw',
            'ksar',
            'lbco',
            'volt',
        ],
    ],
    'npc_patterns' => [
        '/^PU_Pilots-Human-/',           // PU_Pilots-Human-Criminal-Pilot_Light_*
        '/^PU_Human_Enemy_/',            // PU_Human_Enemy_GroundCombat_NPC_*
        '/^PU_Human-/',                  // PU_Human-Headhunter-Worker-*
        '/^AIModule_Unmanned_/',         // AIModule_Unmanned_PU_Advocacy_*
        '/^NPC_/',                       // Generic NPC prefix
        '/_NPC_/',                       // Contains _NPC_
        '/^(?:NineTails|XenoThreat|Crusader|ArcCorp|Hurston)_/i',  // Known NPC factions
    ],
    'batch_size' => env('GAMELOG_BATCH_SIZE', 500), // Number of kills to process per batch
];
