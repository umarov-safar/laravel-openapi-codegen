<?php

namespace LaravelOpenapi\Codegen\Generators;

use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;
use LaravelOpenapi\Codegen\DTO\ExtractedRouteController;
use LaravelOpenapi\Codegen\Utils\NamespaceConvertor;

class ControllerGenerator implements GeneratorInterface
{
    protected ExtractedRouteController $extractedRouteController;

    public function generate(SpecObjectInterface $spec): void
    {
        foreach ($spec->paths as $uri => $path) {
            $this->makeController($uri, $path);
        }
    }

    public function makeController(string $uri, PathItem $pathItem)
    {
        foreach ($pathItem->getOperations() as $operation) {
            if (isset($operation->{'l-og-controller'})) {

                $controller = $operation->{'l-og-controller'};

                if (isset($operation->{'l-og-skip-request'}) && $operation->{'l-og-skip-request'} === false) {
                    $requestNamespaceInfo = NamespaceConvertor::makeRequestNamespace($controller);
                }
            }
        }
    }
}
