<?php

declare(strict_types=1);

use App\Console\Kernel as ConsoleKernel;
use App\Exceptions\ExceptionHandler as Handler;
use App\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Foundation\Application;

$app = new Application($_ENV["APP_BASE_PATH"] ?? dirname(__DIR__));

$app->singleton(HttpKernelContract::class, HttpKernel::class);
$app->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
$app->singleton(ExceptionHandler::class, Handler::class);

return $app;
