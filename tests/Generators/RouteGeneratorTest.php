<?php

namespace Openapi\ServerGenerator\Tests\Generators;

use cebe\openapi\Reader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Openapi\ServerGenerator\Generators\RouteGenerator;
use Openapi\ServerGenerator\Tests\TestCase;

class RouteGeneratorTest extends TestCase
{
    public function test_generate_routes()
    {
        $specObj = Reader::readFromYamlFile(__DIR__.'/../v1/test.yaml');

        $routesTestFile = Config::get('openapi-generator.paths.routes_file');
        $filesystem = new Filesystem();
        $routeGenerator = new RouteGenerator($filesystem);

        $routeGenerator->generate($specObj);

        $this->assertFileExists($routesTestFile);
        $this->assertStringContainsString("App\Http\Controllers\TestController", $filesystem->get($routesTestFile));
        $this->assertStringContainsString('TestController::class', $filesystem->get($routesTestFile));

        //        $filesystem->delete($routesTestFile);
    }
}
