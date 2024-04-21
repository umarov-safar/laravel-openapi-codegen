<?php

namespace LaravelOpenapi\Codegen\Generators;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Filesystem\Filesystem;
use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;
use LaravelOpenapi\Codegen\DTO\NamespaceInfo;
use LaravelOpenapi\Codegen\Utils\ModelSchemaParser;
use LaravelOpenapi\Codegen\Utils\NamespaceConvertor;
use LaravelOpenapi\Codegen\Utils\Stub;

class ResourceGenerator implements GeneratorInterface
{
    use ApplyFileCreatable;

    protected NamespaceInfo $namespaceInfo;

    protected Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function setNamespaceInfo(NamespaceInfo $namespaceInfo): void
    {
        $this->namespaceInfo = $namespaceInfo;
    }

    public function generate(SpecObjectInterface $spec): void
    {
        foreach ($spec->paths as $uri => $path) {
            $this->generateResourceFromPath($path);
        }
    }

    public function generateResourceFromPath(PathItem $pathItem): void
    {
        foreach ($pathItem->getOperations() as $operation) {

            $controller = $operation->{'l-og-controller'} ?? null;
            $skipResource = $operation->{'l-og-skip-resource'} ?? null;

            if (! empty($controller) && $skipResource !== true) {
                $this->setNamespaceInfo(NamespaceConvertor::makeResourceNamespace($controller));
                $this->createResourceFromOperation($operation);
            }
        }
    }

    public function createResourceFromOperation(Operation $operation): void
    {
        if ($this->resourceFileExists() || empty(getAllowedResponseContentTypeMedia($operation))) {
            return;
        }

        $filePath = $this->createFileIfNotExist();
        $stubContent = $this->getResourceStubContent();
        $stubContent = $this->replaceNamespace($stubContent);
        $stubContent = $this->replaceResourceDataResponse($operation, $stubContent);
        $this->filesystem->put($filePath, $stubContent);
    }

    private function resourceFileExists(): bool
    {
        return file_exists(base_path($this->namespaceInfo->filePath));
    }

    private function getResourceStubContent(): string
    {
        return Stub::getStubContent('resource.stub');
    }

    public function replaceNamespace(string $stubContent): string
    {
        return str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
            ],
            [
                $this->namespaceInfo->namespaceWithoutClassName,
                $this->namespaceInfo->className,
            ],
            $stubContent
        );
    }

    public function replaceResourceDataResponse(Operation $operation, string $stubContent): string
    {
        $openapiSchema = getAllowedResponseContentTypeMedia($operation)->getSerializableData();
        $schema = (new ModelSchemaParser($openapiSchema))->parse();

        $responseDataArray = '';

        $properties = $schema->getProperties()['data'] ?? $schema->getProperties();

        if (! $properties->getProperties() && isset($properties->getItems()['properties'])) {
            $properties = $properties->getItems()['properties'];
        } else {
            $properties = $properties->getProperties();
        }

        foreach ($properties as $propertyName => $property) {
            $responseDataArray .= sprintf("\t\t\t'%s' => \$this->%s,\n", $propertyName, $propertyName);
        }

        return str_replace('{{ array }}', trim($responseDataArray), $stubContent);
    }
}
