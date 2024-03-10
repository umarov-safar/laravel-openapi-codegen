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

        $this->mergeConfigFrom(
            __DIR__.'/../config/openapi-generator.php', 'openapi-generator'
        );

        $this->publishes([
            __DIR__.'/../config/openapi-generator.php' => config_path('openapi-generator.php'),
        ], 'config');
    }
}
