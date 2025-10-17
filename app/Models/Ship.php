<?php

namespace App\Models;

use App\Observers\ShipObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([ShipObserver::class])]
class Ship extends Model
{
    protected $table = 'ships';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'slug',
        'name',
        'class_name',
        'description',
        'price_uec',
        'price_usd',
        'icon',
        'version',
    ];

    public function deaths(): HasMany
    {
        return $this->hasMany(Kill::class, 'ship_id');
    }
}
