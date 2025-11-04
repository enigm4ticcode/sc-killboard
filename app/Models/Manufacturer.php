<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacturer extends Model
{
    public const UNKNOWN_CODE = 'NONE';

    public const UNKNOWN_NAME = 'Unknown';

    protected $fillable = [
        'code',
        'name',
    ];

    public function weapons(): HasMany
    {
        return $this->hasMany(Weapon::class);
    }
}
