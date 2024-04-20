<?php

namespace LaravelOpenapi\Codegen\Tests\Generators;

use cebe\openapi\Reader;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Support\Facades\Config;
use LaravelOpenapi\Codegen\Factories\DefaultGeneratorFactory;
use LaravelOpenapi\Codegen\Generators\ControllerGenerator;
use LaravelOpenapi\Codegen\Tests\TestCase;

class ControllerGeneratorTest extends TestCase
{
    protected ControllerGenerator $controllerGenerator;

    protected SpecObjectInterface $spec;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controllerGenerator = DefaultGeneratorFactory::createGenerator('controller');

        $this->spec = Reader::readFromYamlFile(Config::get('openapi-codegen.api_docs_url'));
    }

    public function test_can_generate_controller_from_path_item()
    {
        $pathItem = $this->spec->paths->getPath('/users/{id}/posts/{slug}');

        $this->controllerGenerator->makeController('/users/{id}/posts/{slug}', $pathItem);
        $this->assertTrue(true);
    }
}
