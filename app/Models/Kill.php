<?php

namespace App\Models;

use App\Observers\KillObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([KillObserver::class])]
class Kill extends Model
{
    public const TYPE_FPS = 'fps';

    public const TYPE_VEHICLE = 'vehicle';

    protected $table = 'kills';

    protected $fillable = [
        'id',
        'destroyed_at',
        'ship_id',
        'weapon_id',
        'victim_id',
        'killer_id',
        'type',
        'location',
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

    public function logUpload(): BelongsTo
    {
        return $this->belongsTo(LogUpload::class, 'log_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
