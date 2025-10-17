<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Weapon extends Model
{
    public const TYPE_VEHICLE = 'vehicle';

    public const TYPE_FPS = 'fps';

    public const TYPE_UNKNOWN = 'unknown';

    protected $table = 'weapons';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
        'icon',
    ];

    public function kills(): HasMany
    {
        return $this->hasMany(Kill::class, 'ship_id');
    }
}
