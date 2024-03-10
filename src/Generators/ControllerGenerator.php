<?php

namespace Openapi\ServerGenerator\Generators;

use cebe\openapi\SpecObjectInterface;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;

class ControllerGenerator implements GeneratorInterface
{

    public function generate(SpecObjectInterface $spec): void
    {
        foreach ($spec->paths as $path) {

        }
    }
}