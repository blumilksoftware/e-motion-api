<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $name
 * @property string $url
 * @property string $color
 */
class Provider extends Model
{
    public $incrementing = false;
    protected $primaryKey = "name";
    protected $keyType = "string";

    protected $fillable = [
        "name",
        "url",
        "color",
    ];

    public function cityProvider(): BelongsTo
    {
        return $this->belongsTo(CityProvider::class);
    }
}
