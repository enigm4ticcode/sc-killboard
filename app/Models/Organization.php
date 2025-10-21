<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Organization extends Model
{
    use Searchable;

    public const ORG_NONE = 'NONE';

    public const ORG_REDACTED = 'REDACTED';

    public const REDACTED_ORG_PIC_URL = 'https://cdn.robertsspaceindustries.com/static/images/organization/public-orgs-thumb-redacted-bg.png';

    public const DEFAULT_ORG_PIC_URL = 'https://cdn.robertsspaceindustries.com/static/images/Temp/default-image.png';

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'spectrum_id',
        'icon',
    ];

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'spectrum_id' => $this->spectrum_id,
            'icon' => $this->icon,
        ];
    }

    protected function icon(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => Str::contains($value, 'http')
                ? $value
                : "https://robertsspaceindustries.com/$value",
        );
    }

    public function kills(): HasManyThrough
    {
        return $this->hasManyThrough(
            Kill::class,
            Player::class,
            'organization_id',
            'killer_id',
            'id',
            'id',
        );
    }

    public function losses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Kill::class,
            Player::class,
            'organization_id',
            'victim_id',
            'id',
            'id',
        );
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'organization_id');
    }
}
