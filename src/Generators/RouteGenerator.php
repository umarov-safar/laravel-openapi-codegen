<?php

namespace Openapi\ServerGenerator\Generators;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;
use Openapi\ServerGenerator\Data\HttpMethod;
use Openapi\ServerGenerator\Utils\ExtractedRouteController;
use Openapi\ServerGenerator\Utils\RouteControllerResolver;
use Openapi\ServerGenerator\Utils\Stub;

class RouteGenerator implements GeneratorInterface
{
    /** @var ExtractedRouteController[] */
    private array $routes = [];

    public function __construct(protected Filesystem $filesystem)
    {
    }

    /**
     * Generate routes based on the given specification.
     */
    public function generate(SpecObjectInterface $spec): void
    {
        /** @var PathItem $path */
        foreach ($spec->paths as $path) {
            $this->makeRoute($path);
        }

        $this->generateFile();
    }

    /**
     * Extract controller information from the operation and add it in the routes array
     */
    protected function makeRoute(PathItem $path): void
    {
        foreach (HttpMethod::case() as $methodName) {
            if (isset($path->getOperations()[$methodName])) {
                /** @var Operation $operation */
                $operation = $path->{$methodName};
                $controller = $operation->{'x-og-controller'};
                $extractedController = RouteControllerResolver::extract($controller, $methodName);
                $this->addRoute($extractedController);
            }
        }
    }

    protected function addRoute(ExtractedRouteController $extractedRouteController)
    {
        $this->routes[] = $extractedRouteController;
    }

    /**
     * Generate the file containing the routes.
     */
    protected function generateFile(): void
    {
        $routesStubFile = $this->getRoutesStubFileContent();

        $content = str_replace(
            [
                '{{ namespaces }}',
                '{{ routes }}',
            ],
            [
                $this->getNamespacesAsString(),
                $this->getRoutesAsString(),
            ],
            $routesStubFile
        );

        $this->filesystem->put(Config::get('openapi-generator.paths.routes_file'), $content);
    }

    protected function getRoutesStubFileContent(): string
    {
        return Stub::getStubContent('routes.stub');
    }

    protected function getRouteStubFileContent(): string
    {
        return Stub::getStubContent('route.stub');
    }

    /**
     * Get the namespaces as a string for inclusion in the routes file.
     *
     * @return string The namespaces as a string.
     */
    protected function getNamespacesAsString(): string
    {
        $namespaces = '';
        foreach ($this->routes as $route) {
            if (Str::contains($namespaces, 'use '.$route->namespace)) {
                continue;
            }
            $namespaces .= sprintf("use %s;\n", $route->namespace);
        }

        return $namespaces;
    }

    /**
     * Get the routes as a string for inclusion in the routes file.
     *
     * @return string The routes as a string.
     */
    protected function getRoutesAsString(): string
    {
        $routeStub = $this->getRouteStubFileContent();
        $routes = '';

        foreach ($this->routes as $route) {
            $routes .= str_replace(
                [
                    '{{ method }}',
                    '{{ controller }}',
                    '{{ action }}',
                ],
                [
                    $route->httpMethod,
                    $route->controller,
                    $route->action,
                ],
                $routeStub
            )."\n";
        }

        return $routes;
    }
}
