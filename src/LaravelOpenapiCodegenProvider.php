<?php

namespace LaravelOpenapi\Codegen;

use Illuminate\Support\ServiceProvider;
use LaravelOpenapi\Codegen\Console\Commands\LaravelOpenapiCodegenCommand;

class LaravelOpenapiCodegenProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                LaravelOpenapiCodegenCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/laravel-openapi-codegen.php' => config_path('laravel-openapi-codegen.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-openapi-codegen.php', 'laravel-openapi-codegen'
        );
    }
}
