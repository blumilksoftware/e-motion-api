<?php

declare(strict_types=1);

namespace App\Models;

use App\QueryBuilders\SortQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $latitude
 * @property string $longitude
 * @property int $country_id
 */
class City extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "latitude",
        "longitude",
        "country_id",
    ];

    public function cityAlternativeNames(): HasMany
    {
        return $this->hasMany(CityAlternativeName::class);
    }

    public function cityProviders(): HasMany
    {
        return $this->hasMany(CityProvider::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cityOpinions(): HasMany
    {
        return $this->hasMany(CityOpinion::class);
    }

    public static function query(): Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): SortQuery
    {
        return new SortQuery($query);
    }

    protected static function booted(): void
    {
        static::creating(function ($city): void {
            $city->slug = Str::slug($city->name);
        });
    }
}
