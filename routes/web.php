<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get("/signup", fn(): Response => inertia::render("Auth/Signup"));
Route::get("/login", [LoginController::class, "create"])->name("login");
Route::post("/login", [LoginController::class, "login"]);

Route::post("/register", [RegisterController::class, "store"])->name("register");

Route::get("/", fn() => Inertia::render("Welcome"))->name("home");

Route::get("/login", [LoginController::class, "create"])->name("login");
Route::post("/logout", [LogoutController::class, "logout"])->name("logout");
