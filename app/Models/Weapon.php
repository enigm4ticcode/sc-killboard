<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Weapon extends Model
{
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
