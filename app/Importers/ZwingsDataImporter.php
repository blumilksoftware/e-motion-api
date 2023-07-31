<?php

declare(strict_types=1);

namespace App\Importers;

use App\Models\City;
use App\Models\CityAlternativeName;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class ZwingsDataImporter extends DataImporter
{
    private const PROVIDER_ID = 16;
    private const COUNTRY_NAME = "United Kingdom";

    protected Crawler $sections;

    public function extract(): static
    {
        try {
            $response = $this->client->get("https://www.zwings.co.uk/locations/");
            $html = $response->getBody()->getContents();
        } catch (GuzzleException) {
            $this->createImportInfoDetails("400", self::PROVIDER_ID);

            $this->stopExecution = true;

            return $this;
        }

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter("main > section > div .background-image");

        if (count($this->sections) === 0) {
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

        foreach ($this->sections as $section) {
            $country = null;

            foreach ($section->childNodes as $node) {
                if ($node->nodeName === "div") {
                    foreach ($node->childNodes as $city) {
                        if ($city->nodeName === "p") {
                            $cityName = $city->nodeValue;
                            $city = City::query()->where("name", $cityName)->first();
                            $alternativeCityName = CityAlternativeName::query()->where("name", $cityName)->first();

                            if ($city || $alternativeCityName) {
                                $cityId = $city ? $city->id : $alternativeCityName->city_id;

                                $this->createProvider($cityId, self::PROVIDER_ID);
                                $existingCityProviders[] = $cityId;
                            } else {
                                $country = Country::query()->where("name", self::COUNTRY_NAME)->orWhere("alternative_name", self::COUNTRY_NAME)->first();

                                if ($country) {
                                    $coordinates = $mapboxService->getCoordinatesFromApi($cityName, self::COUNTRY_NAME);
                                    $countCoordinates = count($coordinates);

                                    if (!$countCoordinates) {
                                        $this->createImportInfoDetails("419", self::PROVIDER_ID);
                                    }

                                    $city = City::query()->create([
                                        "name" => $cityName,
                                        "latitude" => ($countCoordinates > 0) ? $coordinates[0] : null,
                                        "longitude" => ($countCoordinates > 0) ? $coordinates[1] : null,
                                        "country_id" => $country->id,
                                    ]);

                                    $this->createProvider($city->id, self::PROVIDER_ID);
                                    $existingCityProviders[] = $city->id;
                                } else {
                                    $this->countryNotFound($cityName, self::COUNTRY_NAME);
                                    $this->createImportInfoDetails("420", self::PROVIDER_ID);
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->deleteMissingProviders(self::PROVIDER_ID, $existingCityProviders);
    }
}
