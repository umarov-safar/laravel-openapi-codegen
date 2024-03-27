<?php

namespace LaravelOpenapi\Codegen\Generators;

use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;

class ControllerGenerator implements GeneratorInterface
{
    public function generate(SpecObjectInterface $spec): void
    {
        foreach ($spec->paths as $path) {
            $this->makeController($path);
        }
    }

    public function makeController(PathItem $pathItem)
    {

    }
}
