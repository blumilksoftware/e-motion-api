<?php

declare(strict_types=1);

namespace App\Importers;

use App\Models\City;
use App\Models\CityAlternativeName;
use App\Models\Country;
use App\Services\MapboxGeocodingService;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class BeamDataImporter extends DataImporter
{
    private const PROVIDER_ID = 17;

    protected Crawler $sections;
    private string $countryName;

    private bool $hasEscooters = false;

    public function extract(): static
    {
        try {
            $html = file_get_contents("https://partner.ridebeam.com/cities");
        } catch (Throwable) {
            $this->createImportInfoDetails("400", self::PROVIDER_ID);

            $this->stopExecution = true;

            return $this;
        }

        $crawler = new Crawler($html);


        $this->sections = $crawler->filter("div .find-beam-box");


        $crawler->filter('br')->each(function (Crawler $brNode) {

            $brDOMNode = $brNode->getNode(0);

            $spaceTextNode = $brDOMNode->ownerDocument->createTextNode('  ');

            $brDOMNode->parentNode->replaceChild($spaceTextNode, $brDOMNode);
        });

        $modifiedHtml = $crawler->html();


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
                if ($node->nodeName === "h4") {
                    $this->countryName = $node->nodeValue;
                }
                if ($node->nodeName === "div") {
                    foreach ($node->childNodes as $div) {
                        if ($div->nodeName === "div") {
                            foreach ($div->childNodes as $city) {
                                if ($city->nodeName === "img" && $city->getAttribute("src") === "https://uploads-ssl.webflow.com/63c4acbedbab5dea8b1b98cd/63d8a5b60da91e7d71298637_map-vehicle-saturn.png") {
                                    $this->hasEscooters = true;
                                } elseif ($city->nodeName === "img" && $city->getAttribute("src") !== "https://uploads-ssl.webflow.com/63c4acbedbab5dea8b1b98cd/63d8a5b60da91e7d71298637_map-vehicle-saturn.png") {
                                    $this->hasEscooters = false;
                                }
                                if ($city->nodeName === "p" && $this->hasEscooters == true) {
                                    $search = ["\u{00A0}", "\u{200D}"];
                                    $cityName = str_replace($search, '', $city->nodeValue);
                                    $cityName = str_replace("Prefecture", '', $city->nodeValue);
                                    $cityName = preg_replace('/[\p{Hiragana}\p{Katakana}\p{Han}]+/u', '', $cityName);
                                    $arrayOfCitiesNames = explode("  ", $cityName);
                                    $arrayOfCitiesNames = array_filter($arrayOfCitiesNames, function ($record) {
                                        return strlen($record) > 1;
                                    });
                                    $arrayOfCitiesNames  = array_filter($arrayOfCitiesNames, function ($record) {
                                        return strpos($record, "•") === false;
                                    });
                                    foreach ($arrayOfCitiesNames as $cityName) {
                                        $cityName = trim($cityName);
                                        $city = City::query()->where("name", $cityName)->first();
                                        $alternativeCityName = CityAlternativeName::query()->where("name", $cityName)->first();

                                        if ($city || $alternativeCityName) {
                                            $cityId = $city ? $city->id : $alternativeCityName->city_id;

                                            $this->createProvider($cityId, self::PROVIDER_ID);
                                            $existingCityProviders[] = $cityId;
                                        } else {
                                            if($this->countryName === "Korea") {
                                                $this->countryName = "South Korea";
                                            }
                                            $country = Country::query()->where("name", $this->countryName)->orWhere("alternative_name", $this->countryName)->first();

                                            if ($country) {
                                                $coordinates = $mapboxService->getCoordinatesFromApi($cityName, $this->countryName);
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
                                                $this->countryNotFound($cityName, $this->countryName);
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
        }
    }
}
