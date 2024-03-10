<?php

namespace Openapi\ServerGenerator\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Concerns\WithWorkbench;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->setBasePath(__DIR__.'/../workbench');

        Config::set('openapi-generator.paths.routes_file', base_path('routes/openapi_test.php'));
        Config::set('openapi-generator.api_docs_url', __DIR__.'/v1/test.yaml');
    }

    protected function getPackageProviders($app)
    {
        return [
            'Openapi\ServerGenerator\OpenapiServerGeneratorProvider',
        ];
    }
}
