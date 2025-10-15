<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'spectrum_id',
        'icon',
    ];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'organization_id');
    }
}
