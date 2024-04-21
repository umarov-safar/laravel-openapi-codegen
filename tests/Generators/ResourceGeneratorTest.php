<?php

namespace LaravelOpenapi\Codegen\Tests\Generators;

use cebe\openapi\Reader;
use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Support\Facades\Config;
use LaravelOpenapi\Codegen\Factories\DefaultGeneratorFactory;
use LaravelOpenapi\Codegen\Generators\ResourceGenerator;
use LaravelOpenapi\Codegen\Tests\TestCase;
use LaravelOpenapi\Codegen\Utils\NamespaceConvertor;

class ResourceGeneratorTest extends TestCase
{
    protected ResourceGenerator $resourceGenerator;

    protected SpecObjectInterface $spec;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resourceGenerator = DefaultGeneratorFactory::createGenerator('resource');

        $this->spec = Reader::readFromYamlFile(Config::get('openapi-codegen.api_docs_url'));
    }

    public function test_can_generate_resource_class_from_path_item()
    {
        $pathItem = $this->spec->paths->getPath('/users');
        $this->resourceGenerator->generateResourceFromPath(new PathItem(['post' => $pathItem->post]));
        $namespaceInfo = NamespaceConvertor::makeResourceNamespace($pathItem->post->{'l-og-controller'});
        $file = base_path('app/Http/Resources/UsersResource.php');
        $this->assertFileExists($file);

        $requestFileContent = file_get_contents($file);
        $this->assertStringContainsString("'name' => \$this->name", $requestFileContent);
        $this->assertStringContainsString("'email' => \$this->email", $requestFileContent);
        $this->assertStringContainsString('App\Http\Resources\UsersResource', $namespaceInfo->namespace);
        unlink($file);
    }

    public function test_does_not_change_resource_if_file_exist()
    {
        $pathItem = $this->spec->paths->getPath('/users');

        $namespaceInfo = NamespaceConvertor::makeResourceNamespace($pathItem->post->{'l-og-controller'});
        $f = fopen(base_path($namespaceInfo->filePath), 'a+');
        fclose($f);

        $this->resourceGenerator->generateResourceFromPath(new PathItem(['post' => $pathItem->post]));

        $file = base_path($namespaceInfo->filePath);
        $this->assertFileExists($file);
        $resourceFileContent = file_get_contents($file);
        $this->assertStringNotContainsString("'name' => \$this->name", $resourceFileContent);
        unlink($file);
    }

    public function test_generate_resource_file_when_response_data_is_array()
    {
        $pathItem = $this->spec->paths->getPath('/users');
        $this->resourceGenerator->generateResourceFromPath(new PathItem(['post' => $pathItem->get]));
        $namespaceInfo = NamespaceConvertor::makeResourceNamespace($pathItem->get->{'l-og-controller'});
        $file = base_path('app/Http/Resources/UsersResource.php');
        $this->assertFileExists($file);

        $this->assertStringContainsString('App\Http\Resources\UsersResource', $namespaceInfo->namespace);
    }
}
