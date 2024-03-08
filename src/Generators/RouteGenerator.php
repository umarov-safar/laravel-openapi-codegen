<?php

namespace Openapi\ServerGenerator\Generators;

use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;

class RouteGenerator implements GeneratorInterface
{
    private array $namespaces = [];

    public function generate(SpecObjectInterface $object): void
    {
        /**
         * @var $route
         * @var PathItem $path
         */
        foreach ($object->paths as $route => $path) {
            dd($path->post);
        }
    }
}
