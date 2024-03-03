<?php

namespace Openapi\ServerGenerator\Factories;

use Openapi\ServerGenerator\Contracts\GeneratorInterface;
use Openapi\ServerGenerator\Data\EntityTypes;
use Openapi\ServerGenerator\Exceptions\GeneratorNotFoundException;
use Openapi\ServerGenerator\Generators\RouteGenerator;

class GeneratorFactory
{
    /**
     * @throws GeneratorNotFoundException
     */
    public static function createGenerator(string $type): GeneratorInterface
    {
        switch ($type) {
            case EntityTypes::ROUTE:
                return new RouteGenerator();
            default:
                throw new GeneratorNotFoundException("Invalid generator type");
        }
    }
}