<?php

namespace Openapi\ServerGenerator\Tests\Utils;

use Openapi\ServerGenerator\Exceptions\RouteControllerInvalidException;
use Openapi\ServerGenerator\Tests\TestCase;
use Openapi\ServerGenerator\Utils\RouteControllerResolver;

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

        $actual = RouteControllerResolver::extract($controller);

        $this->assertSame($expect, $actual);
    }
}
