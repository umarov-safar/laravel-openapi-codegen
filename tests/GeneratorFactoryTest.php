<?php

namespace LaravelOpenapi\Codegen\Tests;

use LaravelOpenapi\Codegen\Data\EntityType;
use LaravelOpenapi\Codegen\Exceptions\GeneratorNotFoundException;
use LaravelOpenapi\Codegen\Factories\DefaultGeneratorFactory;
use LaravelOpenapi\Codegen\Generators\RouteGenerator;

class GeneratorFactoryTest extends TestCase
{
    public function test_return_valid_type_generator()
    {
        $generator = DefaultGeneratorFactory::createGenerator(EntityType::ROUTE);

        $this->assertInstanceOf(RouteGenerator::class, $generator);
    }

    public function test_invalid_type_will_throw_exception()
    {
        $this->expectException(GeneratorNotFoundException::class);

        DefaultGeneratorFactory::createGenerator('not_exists');
    }
}
