<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    protected $table = 'players';

    protected $fillable = [
        'id',
        'user_id',
        'game_id',
        'name',
        'organization_id',
        'avatar',
    ];

    public function kills(): HasMany
    {
        return $this->hasMany(Kill::class, 'killer_id', 'id');
    }

    public function deaths(): HasMany
    {
        return $this->hasMany(Kill::class, 'victim_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
