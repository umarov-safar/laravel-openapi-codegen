<?php

namespace Openapi\ServerGenerator\Generators;

use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;

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
