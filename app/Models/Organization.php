<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
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

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'organization_id');
    }
}
