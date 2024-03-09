<?php

namespace Openapi\ServerGenerator\Utils;

use Illuminate\Support\Str;
use Openapi\ServerGenerator\Exceptions\RouteControllerInvalidException;

class RouteControllerResolver
{
    /**
     * Extract route controller
     * Input: App\Http\Controllers\TestController@index
     * Output: [App\Http\Controllers\TestController, TestController, index] -> [namespace, class, method]
     *
     * @throws RouteControllerInvalidException
     */
    public static function extract(string $controller, ?string $methodName = null): ExtractedRouteController
    {
        if (! Str::contains($controller, '@')) {
            throw new RouteControllerInvalidException(sprintf('The %s is invalid controller', $controller));
        }

        [$namespace, $action] = explode('@', $controller);
        $lastBlackSlash = strrpos($controller, '\\');
        $controller = substr($namespace, $lastBlackSlash + 1);

        return new ExtractedRouteController(
            namespace: $namespace,
            controller: $controller,
            action: $action,
            httpMethod: $methodName
        );
    }
}
