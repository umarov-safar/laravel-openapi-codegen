<?php

namespace LaravelOpenapi\Codegen\Factories;

use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;
use LaravelOpenapi\Codegen\Data\EntityType;
use LaravelOpenapi\Codegen\Exceptions\GeneratorNotFoundException;
use LaravelOpenapi\Codegen\Generators\ControllerGenerator;
use LaravelOpenapi\Codegen\Generators\RequestGenerator;
use LaravelOpenapi\Codegen\Generators\ResourceGenerator;
use LaravelOpenapi\Codegen\Generators\RouteGenerator;

class DefaultGeneratorFactory extends GeneratorFactory
{
    /**
     * @throws GeneratorNotFoundException
     */
    public static function createGenerator(string $type): GeneratorInterface
    {
        return match ($type) {
            EntityType::ROUTE => new RouteGenerator(),
            EntityType::CONTROLLER => new ControllerGenerator(),
            EntityType::REQUEST => new RequestGenerator(),
            EntityType::RESOURCE => new ResourceGenerator(),
            default => throw new GeneratorNotFoundException('Generator not found')
        };
    }
}
