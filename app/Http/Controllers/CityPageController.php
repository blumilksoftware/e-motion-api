<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CityOpinionResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\ProviderResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Provider;
use Inertia\Inertia;

class CityPageController extends Controller
{
    public function index(Country $country, City $city)
    {
        $selectedCity = City::query()
            ->whereBelongsTo($country)
            ->where("id", $city->id)
            ->with("cityProviders", "country")
            ->firstOrFail();

        $providers = Provider::all();

        $cityOpinions = $selectedCity->cityOpinions()->with(["user"])->orderByDesc("updated_at")->paginate("4")->withQueryString();

        return Inertia::render("City/Index", [
            "city" => CityResource::make($selectedCity),
            "providers" => ProviderResource::collection($providers),
            "cityOpinions" => CityOpinionResource::collection($cityOpinions),
        ]);
    }
}
