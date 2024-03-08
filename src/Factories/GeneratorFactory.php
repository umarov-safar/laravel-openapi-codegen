<?php

namespace Openapi\ServerGenerator\Factories;

use Openapi\ServerGenerator\Contracts\GeneratorInterface;

abstract class GeneratorFactory
{
    abstract public static function createGenerator(string $type): GeneratorInterface;
}
