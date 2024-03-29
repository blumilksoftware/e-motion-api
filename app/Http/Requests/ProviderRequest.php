<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Unique;

class ProviderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "regex:/^[A-Z\s]/", "max:100", $this->uniqueRuleForProvider("name")],
            "color" => ["required", "string", "size:7"],
            "file" => [
                "required",
                "mimes:png",
                File::image()
                    ->max(config("app.provider_logo_size"))
                    ->dimensions(Rule::dimensions()->width(config("app.provider_logo_width"))->height(config("app.provider_logo_height"))),
            ],
        ];
    }

    protected function uniqueRuleForProvider(string $column): Unique
    {
        $currentProviderId = $this->route(param: "provider");

        return Rule::unique(table: "providers", column: $column)->ignore($currentProviderId);
    }
}
