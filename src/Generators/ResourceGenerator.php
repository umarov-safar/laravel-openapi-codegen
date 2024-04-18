<?php

namespace LaravelOpenapi\Codegen\Generators;

use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;

class ResourceGenerator implements GeneratorInterface
{
    public function generate(SpecObjectInterface $spec): void
    {
        foreach ($spec->paths as $uri => $path) {
            $this->createResource($path);
        }
    }

    public function createResource(PathItem $pathItem): void
    {
        foreach ($pathItem->getOperations() as $operation) {

            $controller = $operation->{'l-og-controller'} ?? null;
            $skipResource = $operation->{'l-og-skip-resource'} ?? null;

            if (! empty($controller) && $skipResource !== true) {
                $this->createFileIfNotExists();

            }
        }
    }

    protected function createFileIfNotExists()
    {

    }
}
