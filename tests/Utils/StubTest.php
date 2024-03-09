<?php

namespace Openapi\ServerGenerator\Tests\Utils;

use Openapi\ServerGenerator\Tests\TestCase;
use Openapi\ServerGenerator\Utils\Stub;

class StubTest extends TestCase
{
    public function test_stub_exists()
    {
        $exists = Stub::stubExists('routes.stub');
        $this->assertTrue($exists);
    }

    public function test_stub_does_not_exists()
    {
        $exists = Stub::stubExists('not-exists.stub');
        $this->assertFalse($exists);
    }

    public function test_if_stub_file_does_not_exist_will_throw_exception()
    {
        $fileName = 'not-exists.stub';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("The stub $fileName file not exists");

        Stub::getStubFilePath('not-exists.stub');
    }


    public function test_can_get_stub_file_content()
    {
        $content = Stub::getStubContent('routes.stub');

        $this->assertIsString($content);
        $this->assertStringContainsString('{{ namespaces }}', $content);
    }
}
