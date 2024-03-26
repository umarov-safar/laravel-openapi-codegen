<?php

namespace Openapi\ServerGenerator\Tests\Generators;

use cebe\openapi\Reader;
use Illuminate\Support\Facades\Config;
use Openapi\ServerGenerator\Factories\DefaultGeneratorFactory;
use Openapi\ServerGenerator\Generators\RequestGenerator;
use Openapi\ServerGenerator\Tests\TestCase;
use Openapi\ServerGenerator\Utils\RouteControllerResolver;
use Openapi\ServerGenerator\Utils\Stub;

class RequestGeneratorTest extends TestCase
{
    protected RequestGenerator $requestGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestGenerator = DefaultGeneratorFactory::createGenerator('request');
    }

    public function test_can_create_request_file_if_not_exists()
    {
        $method = $this->getMethod(RequestGenerator::class, 'createRequestFileIfNotExists');

        $this->requestGenerator->setExtractedRouteController(RouteControllerResolver::extract('App\Http\Controllers\TestController@create'));

        $filePath = $method->invokeArgs($this->requestGenerator, []);

        $this->assertFileExists($filePath);
    }

    public function test_make_correct_request_namespace()
    {
        $method = $this->getMethod(RequestGenerator::class, 'makeNamespace');
        $this->requestGenerator->setExtractedRouteController(RouteControllerResolver::extract('App\Http\Controllers\TestController@create'));

        $namespace = $method->invokeArgs($this->requestGenerator, []);

        $this->assertSame('App\Http\Requests\CreateTestRequest', $namespace);
    }

    public function test_can_generate_requests_from_path_item()
    {
        $methodForTest = $this->getMethod(RequestGenerator::class, 'generateRequests');
        $spec = Reader::readFromYamlFile(Config::get('openapi-generator.api_docs_url'));

        $pathItem = $spec->paths->getPath('/companies:search');

        $methodForTest->invokeArgs($this->requestGenerator, [$pathItem]);

        $this->assertTrue(true);
    }

    public function test_replace_namespace_correct()
    {
        $methodForCall = $this->getMethod(RequestGenerator::class, 'replaceNamespace');
        $this->requestGenerator->setExtractedRouteController(RouteControllerResolver::extract('App\Http\Controllers\TestController@create'));
        $requestStub = $methodForCall->invokeArgs($this->requestGenerator, [Stub::getStubContent('request.stub')]);

        $this->assertStringContainsString('namespace App\Http\Requests;', $requestStub);
        $this->assertStringContainsString('class CreateTestRequest', $requestStub);
    }

    //    public function test_can_generate_request_file_from_operation()
    //    {
    //        $method = $this->getMethod(RequestGenerator::class, '');
    //    }
}
