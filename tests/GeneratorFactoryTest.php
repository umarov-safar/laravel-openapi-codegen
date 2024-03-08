<?php

namespace Openapi\ServerGenerator\Tests;

use Openapi\ServerGenerator\Data\EntityTypes;
use Openapi\ServerGenerator\Exceptions\GeneratorNotFoundException;
use Openapi\ServerGenerator\Factories\DefaultGeneratorFactory;
use Openapi\ServerGenerator\Factories\GeneratorFactory;
use Openapi\ServerGenerator\Generators\RouteGenerator;

class GeneratorFactoryTest extends TestCase
{
    public function test_return_valid_type_generator()
    {
        $generator = DefaultGeneratorFactory::createGenerator(EntityTypes::ROUTE);

        $this->assertInstanceOf(RouteGenerator::class, $generator);
    }

    public function test_invalid_type_will_throw_exception()
    {
        $this->expectException(GeneratorNotFoundException::class);

        DefaultGeneratorFactory::createGenerator('not_exists');
    }
}
