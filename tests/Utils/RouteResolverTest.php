<?php

namespace LaravelOpenapi\Codegen\Tests\Utils;

use LaravelOpenapi\Codegen\Exceptions\RouteControllerInvalidException;
use LaravelOpenapi\Codegen\Tests\TestCase;
use LaravelOpenapi\Codegen\Utils\RouteControllerResolver;

class RouteResolverTest extends TestCase
{
    public function test_invalid_controller_will_throw_exception()
    {
        $this->expectException(RouteControllerInvalidException::class);
        $controller = "App\Http\Controllers\Api\UserController";

        RouteControllerResolver::extract($controller);
    }

    public function test_extracts_route_correctly()
    {
        $controller = "App\Http\Controllers\Api\UserController@search";
        $expect = [
            'App\Http\Controllers\Api\UserController',
            'UserController',
            'search',
        ];

        $extractedRouteController = RouteControllerResolver::extract($controller);

        $this->assertSame($expect[0], $extractedRouteController->namespace);
        $this->assertSame($expect[1], $extractedRouteController->controller);
        $this->assertSame($expect[2], $extractedRouteController->action);
    }
}
