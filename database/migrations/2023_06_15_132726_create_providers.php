<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("providers", function (Blueprint $table): void {
            $table->string("name")->unique();
            $table->string("url")->nullable();
            $table->string("android_url")->nullable();
            $table->string("ios_url")->nullable();
            $table->string("color");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("providers");
    }
};
