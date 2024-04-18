<?php

namespace LaravelOpenapi\Codegen\Tests\Generators;

use cebe\openapi\Reader;
use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Support\Facades\Config;
use LaravelOpenapi\Codegen\Factories\DefaultGeneratorFactory;
use LaravelOpenapi\Codegen\Generators\ResourceGenerator;
use LaravelOpenapi\Codegen\Tests\TestCase;

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

    public function test_can_generate_resource_from_path_item()
    {
        $pathItem = $this->spec->paths->getPath('/users');
        $this->resourceGenerator->generateFromResourceFromPath(new PathItem(['post' => $pathItem->post]));

        $file = base_path('app/Http/Resources/UsersResource.php');
        $requestFileContent = file_get_contents($file);

        $this->assertFileExists($file);
        $this->assertStringContainsString("'name' => \$this->name", $requestFileContent);
        $this->assertStringContainsString("'email' => \$this->email", $requestFileContent);
    }
}
