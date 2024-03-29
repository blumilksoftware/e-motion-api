<?php

declare(strict_types=1);

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class SortQuery extends Builder
{
    public function search(string $column): self
    {
        if (request()->has("search")) {
            return $this->where($column, "ilike", request("search") . "%");
        }

        return $this;
    }

    public function searchCityNames(): self
    {
        if (request()->has("search")) {
            return $this->where(function ($query): void {
                $query->where("name", "ilike", request("search") . "%")
                    ->orWhereHas("cityAlternativeNames", function ($subQuery): void {
                        $subQuery->where("name", "ilike", request("search") . "%");
                    });
            });
        }

        return $this;
    }

    public function orderByTimeRange(): self
    {
        if (request()->input("order") === "oldest") {
            return $this->orderBy("updated_at");
        }

        return $this->orderByDesc("updated_at");
    }

    public function orderByName(): self
    {
        if (request()->input("order") === "name") {
            return $this->orderBy("name");
        }

        return $this;
    }

    public function orderByProvidersCount(): self
    {
        if (request()->input("order") === "providers") {
            return $this->select("cities.*")
                ->leftJoin("city_providers", "cities.id", "=", "city_providers.city_id")
                ->groupBy("cities.id")
                ->orderByRaw("COUNT(city_providers.provider_name) DESC")
                ->orderBy("cities.name");
        }

        return $this;
    }

    public function orderByCountry()
    {
        if (request()->input("order") === "country") {
            return $this->orderBy("country_id");
        }

        return $this;
    }

    public function orderByEmptyCoordinates()
    {
        if (request()->input("order") === "empty-coordinates") {
            return $this->where("latitude", null)->orWhere("longitude", null);
        }

        return $this;
    }
}
