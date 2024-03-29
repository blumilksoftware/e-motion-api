<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    public function index(): Response
    {
        $countries = Country::query()
            ->search("name")
            ->orderByName()
            ->orderByTimeRange()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render("Countries/Index", [
            "countries" => CountryResource::collection($countries),
        ]);
    }

    public function store(CountryRequest $request): void
    {
        Country::query()->create($request->validated());
    }

    public function update(CountryRequest $request, Country $country): void
    {
        $country->update($request->validated());
    }

    public function destroy(Country $country): void
    {
        $country->delete();
    }
}
