<?php

declare(strict_types=1);

use App\Http\Controllers\CityProviderController;
use App\Http\Controllers\RulesController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->get("/user", fn(Request $request): JsonResponse => new JsonResponse($request->user()));

Route::get("/providers", [CityProviderController::class, "index"]);

Route::get("/rules/{country}/{city}", [RulesController::class, "getRules"]);
