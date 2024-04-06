<?php

namespace LaravelOpenapi\Codegen\Generators;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;
use LaravelOpenapi\Codegen\DTO\RouteConfiguration;
use LaravelOpenapi\Codegen\DTO\RouteInfo;
use LaravelOpenapi\Codegen\Utils\RouteControllerResolver;
use LaravelOpenapi\Codegen\Utils\Stub;

class RouteGenerator implements GeneratorInterface
{
    /** @var RouteInfo[] */
    private array $routes = [];

    protected Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * Generate routes based on the given specification.
     */
    public function generate(SpecObjectInterface $spec): void
    {
        /** @var PathItem $path */
        foreach ($spec->paths as $uri => $path) {
            $this->makeRoute($uri, $path);
        }

        $this->generateFile();
    }

    /**
     * Extract route information from the operation and add it in the routes array
     */
    public function makeRoute(string $uri, PathItem $path): void
    {
        foreach (array_keys($path->getOperations()) as $methodName) {
            /** @var Operation $operation */
            $operation = $path->{$methodName};
            $this->addRoute($this->makeRouteInfo($uri, $operation, $methodName));
        }
    }

    public function makeRouteInfo(
        string $uri,
        Operation $operation,
        string $methodName
    ): RouteInfo {

        $controller = $operation->{'l-og-controller'};
        $extractedController = RouteControllerResolver::extract($controller, $methodName);

        $routeConfiguration = RouteConfiguration::create(
            $methodName,
            ltrim($uri, '/'),
            $operation->{'l-og-route-name'} ?? null,
            $operation->{'l-og-middlewares'} ?? null
        );

        return new RouteInfo($extractedController, $routeConfiguration);
    }

    public function addRoute(RouteInfo $extractedRouteController): void
    {
        $this->routes[] = $extractedRouteController;
    }

    /**
     * Generate the file containing the routes.
     */
    public function generateFile(): void
    {
        $content = $this->generateRoutesFileContent();

        $this->filesystem->put(Config::get('openapi-codegen.paths.routes_file'), $content);
    }

    public function generateRoutesFileContent(): string
    {
        $routesStubFile = $this->getRoutesStubFileContent();

        return str_replace(
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
    }

    public function getRoutesStubFileContent(): string
    {
        return Stub::getStubContent('routes.stub');
    }

    public function getRouteStubFileContent(): string
    {
        return Stub::getStubContent('route.stub');
    }

    /**
     * Get the namespaces as a string for inclusion in the routes file.
     *
     * @return string The namespaces as a string.
     */
    public function getNamespacesAsString(): string
    {
        $namespaces = '';
        foreach ($this->routes as $route) {
            if (Str::contains($namespaces, 'use '.$route->extractedRouteController->namespace)) {
                continue;
            }
            $namespaces .= sprintf("use %s;\n", $route->extractedRouteController->namespace);
        }

        return $namespaces;
    }

    /**
     * Get the routes as a string for inclusion in the routes file.
     *
     * @return string The routes as a string.
     */
    public function getRoutesAsString(): string
    {
        $routes = '';

        foreach ($this->routes as $routeInfo) {
            $routes .= sprintf('%s%s', $this->replaceRouteStub($routeInfo), ";\n");
        }

        return $routes;
    }

    public function replaceRouteStub(RouteInfo $routeInfo): string
    {
        $routeStub = $this->getRouteStubFileContent();
        [$routeAction, $routeNameMethod, $middlewaresMethod] = $this->extractRouteSegments($routeStub);

        $route = str_replace(
            [
                '{{ method }}',
                '{{ uri }}',
                '{{ controller }}',
                '{{ action }}',
            ],
            [
                $routeInfo->routeConfiguration->method,
                $routeInfo->routeConfiguration->uri,
                $routeInfo->extractedRouteController->controller,
                $routeInfo->extractedRouteController->action,
            ],
            $routeAction
        );
        $route .= $this->replaceRouteNameMethod($routeInfo->routeConfiguration, $routeNameMethod);
        $route .= $this->replaceMiddlewareMethod($routeInfo->routeConfiguration, $middlewaresMethod);

        return $route;
    }

    public function replaceRouteNameMethod(RouteConfiguration $routeConfiguration, string $routeNameMethod): string
    {
        if ($routeName = $routeConfiguration->routeName) {
            return '->'.str_replace('{{ routeName }}', $routeName, $routeNameMethod);
        }

        return '';
    }

    public function replaceMiddlewareMethod(RouteConfiguration $routeConfiguration, string $middlewaresMethod): string
    {
        if ($middlewares = $routeConfiguration->middlewares) {
            $middlewares = explode(',', $middlewares);
            $middlewares = array_map(fn ($value) => "'".$value."'", $middlewares);

            $middlewares = implode(', ', $middlewares);

            return '->'.str_replace('{{ middlewares }}', $middlewares, $middlewaresMethod);
        }

        return '';
    }

    public function extractRouteSegments(string $route): array
    {
        return explode('->', $route);
    }
}
