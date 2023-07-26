<?php

declare(strict_types=1);

namespace App\Importers;

require_once "vendor/autoload.php";

use App\Models\City;
use App\Models\CityAlternativeName;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use Throwable;

class BirdDataImporter extends DataImporter
{
    private const PROVIDER_ID = 1;

    protected string $fetchedData;

    public function extract(): static
    {
        try {
            $html = file_get_contents("https://www.bird.co/map/");
        } catch (Throwable) {
            $this->createImportInfoDetails("400", self::PROVIDER_ID);
            $this->stopExecution = true;

            return $this;
        }
        $pattern = '/let features = \[([\s\S]*?)\];/';

        if (preg_match($pattern, $html, $matches)) {
            $this->fetchedData = $matches[1];
        }

        if (!isset($this->fetchedData)) {
            $this->createImportInfoDetails("204", self::PROVIDER_ID);

            $this->stopExecution = true;
        }

        return $this;
    }

    public function transform(): void
    {
        if ($this->stopExecution) {
            return;
        }

        $mapboxService = new MapboxGeocodingService();
        $existingCityProviders = [];

        $this->fetchedData = str_replace(["{", "}", "\t", "type: 'hq'", ",", "\n", "position: new google.maps.LatLng("], "", $this->fetchedData);
        $coordinatesList = explode(")", $this->fetchedData);
        $coordinatesList = array_map("trim", $coordinatesList);

        foreach ($coordinatesList as $coordinates) {
            if ($coordinates) {
                $coords = explode(" ", $coordinates);
                $lat = $coords[0];
                $long = $coords[1];

                $data = $mapboxService->getPlaceFromApi($lat, $long);

                $cityName = $data[0];
                $countryName = $data[1];

                $city = City::query()->where("name", $cityName)->first();
                $alternativeCityName = CityAlternativeName::query()->where("name", $cityName)->first();

                if ($city || $alternativeCityName) {
                    $cityId = $city ? $city->id : $alternativeCityName->city_id;

                    $this->createProvider($cityId, self::PROVIDER_ID);
                    $existingCityProviders[] = $cityId;
                } else {
                    $country = Country::query()->where("name", $countryName)->first();

                    if ($country) {
                        if (!$lat || !$long) {
                            $this->createImportInfoDetails("419", self::PROVIDER_ID);
                        }

                        $city = City::query()->create([
                            "name" => $cityName,
                            "latitude" => $lat,
                            "longitude" => $long,
                            "country_id" => $country->id,
                        ]);

                        $this->createProvider($city->id, self::PROVIDER_ID);
                        $existingCityProviders[] = $city->id;
                    } else {
                        $this->countryNotFound($cityName, $countryName);
                        $this->createImportInfoDetails("420", self::PROVIDER_ID);
                    }
                }
            }
        }
        $this->deleteMissingProviders(self::PROVIDER_ID, $existingCityProviders);
    }
}
