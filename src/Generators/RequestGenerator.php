<?php

namespace Openapi\ServerGenerator\Generators;

use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Filesystem\Filesystem;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;
use Openapi\ServerGenerator\Data\HttpMethod;
use Openapi\ServerGenerator\DTO\ExtractedRouteController;
use Openapi\ServerGenerator\Utils\RouteControllerResolver;

class RequestGenerator implements GeneratorInterface
{
    protected Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function generate(SpecObjectInterface $spec): void
    {
        foreach ($spec->paths as $path) {
            $this->generateRequest($path);
        }
    }

    protected function generateRequest(PathItem $pathItem)
    {
        foreach (HttpMethod::case() as $methodName) {
            if (isset($pathItem->{$name})) {
                $operation = $pathItem->{$methodName};

                $controller = $operation->{'x-og-controller'} ?? null;
                $requestNotSkipped = $operation->{'x-og-skip-request'} ?? null;

                if ($controller && $requestNotSkipped) {
                    $extractedRequest = RouteControllerResolver::extract($controller, $methodName);

                    $this->createRequestFileIfNotExists($extractedRequest);
                }
            }
        }
    }

    protected function createRequestFileIfNotExists(ExtractedRouteController $extractedRouteController): bool
    {
        $requestNamespace = $this->makeNamespace($extractedRouteController);

        $filePath = normalizePathSeparators(lcfirst($requestNamespace).'.php');
        $filePath = base_path($filePath);
        $directory = dirname($filePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filePath)) {
            return true;
        }

        $newFile = fopen($filePath, 'w');
        fclose($newFile);

        return true;
    }

    protected function makeNamespace(ExtractedRouteController $extractedRouteController): string
    {
        // replace Controllers directory with Requests directory in controller namespace
        $requestNamespace = str_replace('Controllers\\', 'Requests\\', $extractedRouteController->namespace);

        // make request class name from controller
        $requestClassName = sprintf('%s%s',
            ucfirst($extractedRouteController->action),
            str_replace('Controller', 'Request', $extractedRouteController->controller)
        );

        return str_replace($extractedRouteController->controller, $requestClassName, $requestNamespace);
    }
}
