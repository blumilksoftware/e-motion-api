<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    public function index(): Response
    {
        $countries = CountryResource::collection(Country::all()->sortBy("name"));

        return Inertia::render("Countries", [
            "countries" => $countries,
        ]);
    }

    public function store(CountryRequest $request): void
    {
        Country::query()->create($request->validated());
    }

    public function show(Country $country): CountryResource
    {
        return CountryResource::make($country);
    }

    public function update(UpdateCountryRequest $request): void
    {
        Country::query()->update($request->validated());
    }

    public function destroy(Country $country): void
    {
        $country->delete();
    }
}
