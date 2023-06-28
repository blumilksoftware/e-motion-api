<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Str::random(10);

        User::create([
            "name" => "admin5",
            "email" => "admin5@example.com",
            "email_verified_at" => now(),
            "password" => Hash::make($password),
            "remember_token" => Str::random(10),
            "role" => "admin",
        ]);
    }
}
