<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Player extends Model
{
    use Notifiable, Searchable;

    protected $table = 'players';

    protected $fillable = [
        'id',
        'user_id',
        'game_id',
        'name',
        'organization_id',
        'avatar',
    ];

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'game_id' => $this->game_id,
            'name' => $this->name,
            'organization_id' => $this->organization_id,
            'avatar' => $this->avatar,
        ];
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => ! empty($value) && Str::contains($value, 'http')
                ? $value
                : "https://robertsspaceindustries.com/$value"
        );
    }

    public function kills(): HasMany
    {
        return $this->hasMany(Kill::class, 'killer_id', 'id');
    }

    public function losses(): HasMany
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
