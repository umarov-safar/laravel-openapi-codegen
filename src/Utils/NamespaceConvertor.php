<?php

namespace LaravelOpenapi\Codegen\Utils;

use LaravelOpenapi\Codegen\DTO\NamespaceInfo;

class NamespaceConvertor
{
    public static function makeRequestNamespace(string $controller): NamespaceInfo
    {
        return self::makeNamespaceInfoFor($controller, 'request');
    }

    public static function makeResourceNamespace(string $controller): NamespaceInfo
    {
        return self::makeNamespaceInfoFor($controller, 'resource');
    }

    public static function makeNamespaceInfoForController(string $controller): NamespaceInfo
    {
        return self::makeNamespaceInfoFor($controller, 'controller');
    }

    /**
     * This function make namespace info from controller for request, resource and etc
     */
    public static function makeNamespaceInfoFor(string $controller, string $type): NamespaceInfo
    {
        $extractedRouteController = RouteControllerResolver::extract($controller);
        switch ($type) {
            case 'request':
                $namespace = str_replace('Controllers\\', 'Requests\\', $extractedRouteController->namespace);
                $className = sprintf('%s%s',
                    ucfirst($extractedRouteController->action),
                    str_replace('Controller', 'Request', $extractedRouteController->controller)
                );
                break;
            case 'resource':
                $namespace = str_replace('Controllers\\', 'Resources\\', $extractedRouteController->namespace);
                $className = str_replace('Controller', 'Resource', $extractedRouteController->controller);
                break;
            case 'controller':
                $namespace = $extractedRouteController->namespace;
                $className = $extractedRouteController->controller;
                break;
            default:
                throw new \Exception("$type: must be one of resource or request");
        }

        $namespace = str_replace($extractedRouteController->controller, $className, $namespace);
        $namespaceWithoutClassName = str_replace($className, '', $namespace);
        $filePath = normalizePathSeparators(lcfirst($namespace)).'.php';

        return new NamespaceInfo(
            $className,
            $namespace,
            $filePath,
            trim($namespaceWithoutClassName, '\\')
        );
    }
}
