<?php

namespace LaravelOpenapi\Codegen\Utils;

use Illuminate\Support\Str;
use LaravelOpenapi\Codegen\DTO\ExtractedRouteController;
use LaravelOpenapi\Codegen\Exceptions\RouteControllerInvalidException;

class RouteControllerResolver
{
    /**
     * Extract route controller
     * Input: App\Http\Controllers\TestController@index
     * Output: [App\Http\Controllers\TestController, TestController, index] -> [namespace, class, method]
     *
     * @throws RouteControllerInvalidException
     */
    public static function extract(
        string $controller,
    ): ExtractedRouteController
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
        );
    }
}
