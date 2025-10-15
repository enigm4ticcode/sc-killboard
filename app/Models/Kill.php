<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kill extends Model
{
    protected $table = 'kills';

    protected $fillable = [
        'id',
        'destroyed_at',
        'ship_id',
        'weapon_id',
        'victim_id',
        'killer_id',
    ];

    public function victim(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'victim_id');
    }

    public function killer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'killer_id');
    }

    public function ship(): BelongsTo
    {
        return $this->belongsTo(Ship::class, 'ship_id');
    }

    public function weapon(): BelongsTo
    {
        return $this->belongsTo(Weapon::class, 'weapon_id');
    }
}
