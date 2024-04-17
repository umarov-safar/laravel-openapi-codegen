<?php

namespace LaravelOpenapi\Codegen\Tests\Utils;

use LaravelOpenapi\Codegen\Tests\TestCase;
use LaravelOpenapi\Codegen\Utils\NamespaceConvertor;

class NamespaceConvertorTest extends TestCase
{
    public function test_can_convert_request_namespace_form_controller()
    {
        $controller = 'App\Http\Controllers\UserController@create';

        $requestNamespaceInfo = NamespaceConvertor::makeRequestNamespace($controller);

        $this->assertSame('App\Http\Requests\CreateUserRequest', $requestNamespaceInfo->namespace);
        $this->assertSame('App\Http\Requests', $requestNamespaceInfo->namespaceWithoutClassName);
        $this->assertSame('CreateUserRequest', $requestNamespaceInfo->className);
        $this->assertSame('app/Http/Requests/CreateUserRequest.php', $requestNamespaceInfo->filePath);
    }
}
