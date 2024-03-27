<?php

namespace Openapi\ServerGenerator;

use Illuminate\Support\ServiceProvider;
use Openapi\ServerGenerator\Console\Commands\OpenapiServerGeneratorCommand;

class OpenapiServerGeneratorProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                OpenapiServerGeneratorCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/rest-generator.php' => config_path('rest-generator.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/rest-generator.php', 'rest-generator'
        );
    }
}
