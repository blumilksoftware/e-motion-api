<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "url" => $this->url,
            "android_url" => $this->android_url,
            "ios_url" => $this->ios_url,
            "color" => $this->color,
        ];
    }
}
