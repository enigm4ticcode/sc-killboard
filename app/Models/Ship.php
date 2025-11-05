<?php

namespace App\Models;

use App\Observers\ShipObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $appends = ['icon_url'];

    public function deaths(): HasMany
    {
        return $this->hasMany(Kill::class, 'ship_id');
    }

    /**
     * Get the proper URL for the ship icon
     * Handles both external URLs and local storage paths
     */
    protected function iconUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->icon) {
                    return null;
                }

                // If it's already a full URL, return as-is
                if (str_starts_with($this->icon, 'http://') || str_starts_with($this->icon, 'https://')) {
                    return $this->icon;
                }

                // Otherwise, it's a storage path - convert to URL
                return asset($this->icon);
            }
        );
    }
}
