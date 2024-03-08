<?php

namespace Openapi\ServerGenerator\Factories;

use Openapi\ServerGenerator\Contracts\GeneratorInterface;
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
            'route' => new RouteGenerator(),
            default => throw new GeneratorNotFoundException('Generator not found')
        };
    }
}
