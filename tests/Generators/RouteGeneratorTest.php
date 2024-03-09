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
        $specObj = Reader::readFromYamlFile(__DIR__ . '/../v1/test.yaml');
        $routeTestFile = base_path('routes/openapi_test.php');

        Config::set('openapi-generator.paths.routes_file', $routeTestFile);
        $filesystem = new Filesystem();
        $routeGenerator = new RouteGenerator($filesystem);

        $routeGenerator->generate($specObj);

        $this->assertFileExists($routeTestFile);
        $this->assertStringContainsString("App\Http\TestController", $filesystem->get($routeTestFile));
        $this->assertStringContainsString("TestController::class", $filesystem->get($routeTestFile));

        $filesystem->delete(Config::get('openapi-generator.paths.routes_file'));
    }

}