<?php

namespace Openapi\ServerGenerator\Tests;

use Openapi\ServerGenerator\Data\EntityTypes;
use Openapi\ServerGenerator\Exceptions\GeneratorNotFoundException;
use Openapi\ServerGenerator\Factories\GeneratorFactory;
use Openapi\ServerGenerator\Generators\RouteGenerator;

class GeneratorFactoryTest extends TestCase
{

    public function test_return_correct_type()
    {
        $generator = GeneratorFactory::createGenerator(EntityTypes::ROUTE);

        $this->assertInstanceOf(RouteGenerator::class, $generator);
    }

    public function test_exception_invalid_type()
    {
        $this->expectException(GeneratorNotFoundException::class);

        GeneratorFactory::createGenerator('not_exists');
    }

}