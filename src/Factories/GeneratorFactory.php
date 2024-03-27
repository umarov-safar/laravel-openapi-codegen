<?php

namespace LaravelOpenapi\Codegen\Factories;

use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;

abstract class GeneratorFactory
{
    abstract public static function createGenerator(string $type): GeneratorInterface;
}
