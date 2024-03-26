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
        $specObj = Reader::readFromYamlFile(__DIR__.'/../v1/index.yaml');

        $routesTestFile = Config::get('openapi-generator.paths.routes_file');
        $filesystem = new Filesystem();
        $routeGenerator = new RouteGenerator();

        $routeGenerator->generate($specObj);

        $this->assertFileExists($routesTestFile);
        $this->assertStringContainsString("App\Http\ApiV1\Modules\Companies\Controllers\CompaniesController", $filesystem->get($routesTestFile));
        $this->assertStringContainsString('CompaniesController::class', $filesystem->get($routesTestFile));

        //        $filesystem->delete($routesTestFile);
    }
}
