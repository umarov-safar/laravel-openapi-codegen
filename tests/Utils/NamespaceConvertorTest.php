<?php

namespace LaravelOpenapi\Codegen\Tests\Utils;

use LaravelOpenapi\Codegen\Tests\TestCase;
use LaravelOpenapi\Codegen\Utils\NamespaceConvertor;

class NamespaceConvertorTest extends TestCase
{
    public function test_can_convert_request_namespace_from_controller()
    {
        $controller = 'App\Http\Controllers\UserController@create';

        $requestNamespaceInfo = NamespaceConvertor::makeRequestNamespace($controller);

        $this->assertSame('App\Http\Requests\CreateUserRequest', $requestNamespaceInfo->namespace);
        $this->assertSame('App\Http\Requests', $requestNamespaceInfo->namespaceWithoutClassName);
        $this->assertSame('CreateUserRequest', $requestNamespaceInfo->className);
        $this->assertSame('app/Http/Requests/CreateUserRequest.php', $requestNamespaceInfo->filePath);
    }

    public function test_can_convert_namespace_from_controller_with_type()
    {
        $controller = 'App\Http\Controllers\UserController@test';

        $requestNamespaceInfo = NamespaceConvertor::makeNamespaceInfoFor($controller, 'resource');

        $this->assertSame('App\Http\Resources\UserResource', $requestNamespaceInfo->namespace);
        $this->assertSame('App\Http\Resources', $requestNamespaceInfo->namespaceWithoutClassName);
        $this->assertSame('UserResource', $requestNamespaceInfo->className);
        $this->assertSame('app/Http/Resources/UserResource.php', $requestNamespaceInfo->filePath);
    }
}
