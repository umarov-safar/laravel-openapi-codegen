<?php

namespace LaravelOpenapi\Codegen\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Concerns\WithWorkbench;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->setBasePath(__DIR__.'/../workbench');

        Config::set('openapi-codegen.paths.routes_file', base_path('routes/openapi_test.php'));
        Config::set('openapi-codegen.api_docs_url', __DIR__.'/v1/index.yaml');
    }

    protected function getPackageProviders($app)
    {
        return [
            'LaravelOpenapi\Codegen\LaravelOpenapiCodegenProvider',
        ];
    }

    /**
     * @throws \ReflectionException
     */
    protected function getMethod(string $className, string $method)
    {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }
}
