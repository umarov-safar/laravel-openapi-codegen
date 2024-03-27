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
        foreach (array_keys($path->getOperations()) as $methodName) {
            /** @var Operation $operation */
            $operation = $path->{$methodName};
            $this->addRoute($this->makeRouteInfoObject($operation, $methodName));
        }
    }

    protected function makeRouteInfoObject(Operation $operation, string $methodName): RouteInfo
    {
        $controller = $operation->{'x-og-controller'};
        $extractedController = RouteControllerResolver::extract($controller, $methodName);

        $routeConfiguration = RouteConfiguration::create(
            $operation->{'x-og-route-name'} ?? null,
            $operation->{'x-og-middlewares'} ?? null
        );

        return new RouteInfo($extractedController, $routeConfiguration);
    }

    protected function addRoute(RouteInfo $extractedRouteController): void
    {
        $this->routes[] = $extractedRouteController;
    }

    /**
     * Generate the file containing the routes.
     */
    protected function generateFile(): void
    {
        $content = $this->generateRoutesFileContent();

        $this->filesystem->put(Config::get('laravel-openapi-codegen.paths.routes_file'), $content);
    }

    protected function generateRoutesFileContent(): string
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
    protected function getRoutesAsString(): string
    {
        $routes = '';
        $routeStub = $this->getRouteStubFileContent();
        [$routeAction, $routeNameMethod, $middlewaresMethod] = $this->extractRouteSegments($routeStub);

        foreach ($this->routes as $routeInfo) {
            $routes .= sprintf('%s%s%s%s',
                $this->replaceAction($routeInfo, $routeAction),
                $this->replaceRouteNameMethod($routeInfo, $routeNameMethod),
                $this->replaceMiddlewareMethod($routeInfo, $middlewaresMethod),
                ";\n"
            );
        }

        return $routes;
    }

    protected function replaceAction(RouteInfo $routeInfo, string $routeAction): string
    {
        return str_replace(
            [
                '{{ method }}',
                '{{ controller }}',
                '{{ action }}',
            ],
            [
                $routeInfo->extractedRouteController->httpMethod,
                $routeInfo->extractedRouteController->controller,
                $routeInfo->extractedRouteController->action,
            ],
            $routeAction
        );
    }

    protected function replaceRouteNameMethod(RouteInfo $routeInfo, string $routeNameMethod): string
    {
        if ($routeName = $routeInfo->routeConfiguration->routeName) {
            return '->'.str_replace('{{ routeName }}', "'$routeName'", $routeNameMethod);
        }

        return '';
    }

    protected function replaceMiddlewareMethod(RouteInfo $routeInfo, string $middlewaresMethod): string
    {
        if ($middlewares = $routeInfo->routeConfiguration->middlewares) {
            $middlewares = explode(',', $middlewares);
            $middlewares = array_map(fn ($value) => "'".$value."'", $middlewares);

            $middlewares = implode(',', $middlewares);

            return '->'.str_replace('{{ middlewares }}', $middlewares, $middlewaresMethod);
        }

        return '';
    }

    protected function extractRouteSegments(string $route): array
    {
        return explode('->', $route);
    }
}
