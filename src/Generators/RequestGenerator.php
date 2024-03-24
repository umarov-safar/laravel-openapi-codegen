<?php

namespace Openapi\ServerGenerator\Generators;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Filesystem\Filesystem;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;
use Openapi\ServerGenerator\Data\MediaType;
use Openapi\ServerGenerator\DTO\ExtractedRouteController;
use Openapi\ServerGenerator\DTO\OpenapiProperty;
use Openapi\ServerGenerator\Utils\ModelSchemaParser;
use Openapi\ServerGenerator\Utils\RouteControllerResolver;
use Openapi\ServerGenerator\Utils\Stub;

class RequestGenerator implements GeneratorInterface
{
    protected Filesystem $filesystem;

    protected array $methodsForGenerate = ['put', 'patch', 'post', 'delete'];

    protected ExtractedRouteController $extractedRouteController;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function setExtractedRouteController(ExtractedRouteController $extractedRouteController): void
    {
        $this->extractedRouteController = $extractedRouteController;
    }

    public function generate(SpecObjectInterface $spec): void
    {
        foreach ($spec->paths as $path) {
            $this->generateRequests($path);
        }
    }

    protected function generateRequests(PathItem $pathItem)
    {
        foreach (array_keys($pathItem->getOperations()) as $methodName) {
            $operation = $pathItem->{$methodName};

            $controller = $operation->{'x-og-controller'} ?? null;
            $skipRequest = $operation->{'x-og-skip-request'} ?? null;

            /**
             * For generating requests, we follow these rules:
             * 1. Ensure the controller exists. Requests will only be generated for controllers that we can confirm exist.
             * 2.1 Check if the request method is in the list of methods by default and not skipped for generation.
             * 2.2 Or Alternatively, check if the flag x-og-skip-request is set to false. If skip-request is set to false,
             * we generate requests even for methods not in the default list.
             */
            if ($controller && (
                (in_array($methodName, $this->methodsForGenerate) && $skipRequest !== true) ||
                $skipRequest === false
            )) {

                $this->extractedRouteController = RouteControllerResolver::extract($controller, $methodName);
                $this->generateRequestForOperation($operation);
            }
        }
    }

    protected function generateRequestForOperation(Operation $operation): void
    {
        if ($this->requestFileExists()) {
            return;
        }

        $filePath = $this->createRequestFileIfNotExists();
        $stubContent = $this->getRequestStubContent();
        $stubContent = $this->replaceNamespace($stubContent);
        $stubContent = $this->replaceRules($operation, $stubContent);
        $this->filesystem->put($filePath, $stubContent);
    }

    protected function replaceRules(Operation $operation, string $stubContent)
    {
        $rules = $this->getRules($operation);

        return str_replace('{{ rules }}', $rules, $stubContent);
    }

    protected function replaceNamespace(string $stubContent)
    {
        $namespaceParts = explode('\\', $this->makeNamespace());
        $requestClasName = array_pop($namespaceParts);

        return str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
            ],
            [
                implode('\\', $namespaceParts),
                $requestClasName,
            ],
            $stubContent
        );
    }

    protected function getRules(Operation $operation): string
    {
        $openapiSchema = $operation->requestBody->content[MediaType::APPLICATION_JSON]->getSerializableData()->schema;
        $schema = (new ModelSchemaParser($openapiSchema))->parse();

        $rules = '';

        foreach ($schema->getProperties() as $propertyName => $property) {
            if (in_array($propertyName, $schema->getRequired())) {
                $property->addValidationItem('required');
            }
            $rules .= sprintf("\t\t\t'%s' => %s", $propertyName, $this->makeRule($property));
        }

        return trim($rules);
    }

    public function makeRule(OpenapiProperty $property)
    {
        $rules = array_map(function ($item) {
            return "'".$item."'";
        }, $property->getValidation());

        return '['.implode(', ', $rules)."], \n";
    }

    public function requestFileExists(): bool
    {
        return file_exists($this->getFilePath());
    }

    protected function createRequestFileIfNotExists(): string
    {
        $requestNamespace = $this->makeNamespace();

        $filePath = normalizePathSeparators(lcfirst($requestNamespace).'.php');
        $filePath = base_path($filePath);
        $directory = dirname($filePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filePath)) {
            return $filePath;
        }

        $newFile = fopen($filePath, 'w');
        fclose($newFile);

        return $filePath;
    }

    protected function makeNamespace(): string
    {
        // replace Controllers directory with Requests directory in controller namespace
        $requestNamespace = str_replace('Controllers\\', 'Requests\\', $this->extractedRouteController->namespace);

        // make request class name from controller
        $requestClassName = sprintf('%s%s',
            ucfirst($this->extractedRouteController->action),
            str_replace('Controller', 'Request', $this->extractedRouteController->controller)
        );

        return str_replace($this->extractedRouteController->controller, $requestClassName, $requestNamespace);
    }

    protected function getFilePath(): string
    {
        $requestFilePath = lcfirst($this->makeNamespace()).'.php';

        return normalizePathSeparators(base_path($requestFilePath));
    }

    protected function getRequestStubContent(): string
    {
        return Stub::getStubContent('request.stub');
    }
}
