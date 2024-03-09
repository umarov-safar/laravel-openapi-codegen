<?php

namespace Openapi\ServerGenerator\Factories;

use Illuminate\Filesystem\Filesystem;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;
use Openapi\ServerGenerator\Data\EntityType;
use Openapi\ServerGenerator\Exceptions\GeneratorNotFoundException;
use Openapi\ServerGenerator\Generators\RouteGenerator;

class DefaultGeneratorFactory extends GeneratorFactory
{
    /**
     * @throws GeneratorNotFoundException
     */
    public static function createGenerator(string $type): GeneratorInterface
    {
        return match ($type) {
            EntityType::ROUTE => new RouteGenerator(new Filesystem()),
            default => throw new GeneratorNotFoundException('Generator not found')
        };
    }
}
