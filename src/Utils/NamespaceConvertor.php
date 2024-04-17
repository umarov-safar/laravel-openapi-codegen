<?php

namespace LaravelOpenapi\Codegen\Utils;

use LaravelOpenapi\Codegen\DTO\NamespaceInfo;

class NamespaceConvertor
{
    public static function makeRequestNamespace(string $controller): NamespaceInfo
    {
        $extractedRouteController = RouteControllerResolver::extract($controller);

        // replace Controllers directory into Requests directory in controller namespace
        $requestNamespace = str_replace('Controllers\\', 'Requests\\', $extractedRouteController->namespace);
        // make Request name class from controller name and action
        $requestClassName = sprintf('%s%s',
            ucfirst($extractedRouteController->action),
            str_replace('Controller', 'Request', $extractedRouteController->controller)
        );

        $fullRequestNamespace = str_replace($extractedRouteController->controller, $requestClassName, $requestNamespace);
        $requestNamespaceWithoutClassName = str_replace($extractedRouteController->controller, '', $requestNamespace);

        $filePath = normalizePathSeparators(lcfirst($fullRequestNamespace)).'.php';

        return new NamespaceInfo(
            $requestClassName,
            $fullRequestNamespace,
            $filePath,
            trim($requestNamespaceWithoutClassName, '\\')
        );
    }
}
