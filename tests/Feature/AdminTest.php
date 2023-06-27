<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function testAdminCanAccessDashboard(): void
    {
        $this->seed(); 

        $admin = User::whereHas("roles", function ($query): void {
            $query->where("name", "admin");
        })->first();

        $response = $this->actingAs($admin)->get("/dashboard");

        $response->assertStatus(200);
    }

    public function testAdminCanAccessCountry(): void
    {
        $this->seed();

        $admin = User::whereHas("roles", function ($query): void {
            $query->where("name", "admin");
        })->first();

        $response = $this->actingAs($admin)->get("/countries");

        $response->assertStatus(200);
    }
}
