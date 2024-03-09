<?php

namespace Openapi\ServerGenerator\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->setBasePath(__DIR__.'/../workbench');
    }

    protected function getPackageProviders($app)
    {
        return [
            'Openapi\ServerGenerator\OpenapiServerGeneratorProvider',
        ];
    }
}
