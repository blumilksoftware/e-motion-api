<?php

declare(strict_types=1);

namespace App\Importers;
require_once 'vendor/autoload.php';

use App\Models\City;
use App\Models\CityAlternativeName;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class NeuronDataImporter extends DataImporter
{
    private const PROVIDER_ID = 9;

    protected Crawler $sections;

    public function extract(): static
    {
        try {
            $html = file_get_contents("https://www.scootsafe.com/");
        } catch (Throwable) {
            $this->createImportInfoDetails("400", self::PROVIDER_ID);

            $this->stopExecution = true;

            return $this;
        }

        $crawler = new Crawler($html);
        $this->sections = $crawler->filter("nav.navbar > div > ul > li.nav-city > div > div > ul > li > button");
        // nav.navbar > div > ul > li.nav-city > div > div > ul > li > button
        // #search-199-element > li
        // html body div#app.scootsafe-app div.scootsafe-pagecontent nav.navbar.container div.row ul.navbar-nav li.nav-item.nav-city div.dropdown.dropdown-region div.dropdown-menu.dropdown-menu-end ul#search-199-element.dropdown-result li

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
            $cityName = trim($section->nodeValue);
            $iso = $section->getElementsByTagName('img')->getAttribute('alt');

            $city = City::query()->where("name", $cityName)->first();
            $alternativeCityName = CityAlternativeName::query()->where("name", $cityName)->first();

            if ($city || $alternativeCityName) {
                $cityId = $city ? $city->id : $alternativeCityName->city_id;

                $this->createProvider($cityId, self::PROVIDER_ID);
                $existingCityProviders[] = $cityId;
            } else {
                $country = Country::query()->where("iso", $iso)->first();

                if ($country) {
                    $countryName = $country->getAttribute("name");
                    $coordinates = $mapboxService->getCoordinatesFromApi($cityName, $countryName);
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
                    $this->countryNotFound($cityName, $iso);
                    $this->createImportInfoDetails("420", self::PROVIDER_ID);
                }
            }
        }
        $this->deleteMissingProviders(self::PROVIDER_ID, $existingCityProviders);
    }
}
