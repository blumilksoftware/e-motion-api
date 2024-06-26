<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    "name" => env("APP_NAME", "Laravel"),

    "env" => env("APP_ENV", "production"),

    "debug" => (bool)env("APP_DEBUG", false),

    "url" => env("APP_URL", "http://localhost"),

    "asset_url" => env("ASSET_URL"),

    "timezone" => "UTC",

    "locale" => "en",

    "supported_locales" => [
        "en",
        "pl",
    ],

    "fallback_locale" => "en",

    "faker_locale" => "en_US",

    "key" => env("APP_KEY"),

    "cipher" => "AES-256-CBC",

    "maintenance" => [
        "driver" => "file",
    ],

    "providers" => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
    ])->toArray(),

    "aliases" => Facade::defaultAliases()->merge([
    ])->toArray(),

    "provider_logo_size" => 10 * 1024,
    "provider_logo_width" => 150,
    "provider_logo_height" => 100,
];
