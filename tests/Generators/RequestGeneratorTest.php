<?php

namespace Openapi\ServerGenerator\Tests\Generators;

use cebe\openapi\Reader;
use Illuminate\Support\Facades\Config;
use Openapi\ServerGenerator\Factories\DefaultGeneratorFactory;
use Openapi\ServerGenerator\Generators\RequestGenerator;
use Openapi\ServerGenerator\Tests\TestCase;
use Openapi\ServerGenerator\Utils\RouteControllerResolver;

class RequestGeneratorTest extends TestCase
{
    protected RequestGenerator $requestGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestGenerator = DefaultGeneratorFactory::createGenerator('request');
    }

    //    public function test_generate_request()
    //    {
    //        $spec = Reader::readFromYamlFile(Config::get('openapi-generator.api_docs_url'));
    //
    //        $this->requestGenerator->generate($spec);
    //        $this->assertTrue(true);
    //    }

    public function test_can_generate_request_file()
    {
        $method = $this->getMethod(RequestGenerator::class, 'createRequestFileIfNotExists');
        $method->invokeArgs($this->requestGenerator, [RouteControllerResolver::extract('App\Http\Controllers\TestController@create')]);

        $this->assertFileExists(normalizePathSeparators(app_path('Http\Requests\CreateTestRequest.php')));
    }
}
