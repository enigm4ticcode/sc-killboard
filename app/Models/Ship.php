<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ship extends Model
{
    protected $table = 'ships';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
        'price_uec',
        'price_usd',
        'icon',
    ];

    public function deaths(): HasMany
    {
        return $this->hasMany(Kill::class, 'ship_id');
    }
}
