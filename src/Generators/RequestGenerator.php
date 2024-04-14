<?php

namespace LaravelOpenapi\Codegen\Generators;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Filesystem\Filesystem;
use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;
use LaravelOpenapi\Codegen\Data\MediaType;
use LaravelOpenapi\Codegen\DTO\ExtractedRouteController;
use LaravelOpenapi\Codegen\DTO\OpenapiProperty;
use LaravelOpenapi\Codegen\Utils\ModelSchemaParser;
use LaravelOpenapi\Codegen\Utils\RouteControllerResolver;
use LaravelOpenapi\Codegen\Utils\Stub;

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

    public function generateRequests(PathItem $pathItem): void
    {
        foreach (array_keys($pathItem->getOperations()) as $methodName) {
            /** @var Operation $operation */
            $operation = $pathItem->{$methodName};

            $controller = $operation->{'l-og-controller'} ?? null;
            $skipRequest = $operation->{'l-og-skip-request'} ?? null;

            /**
             * For generating requests, we follow these rules:
             * 1. Ensure the controller exists. Requests will only be generated for controllers that we can confirm exist.
             * 2.1 Check if the request method is in the list of methods by default and not skipped for generation.
             * 2.2 Or Alternatively, check if the flag l-og-skip-request is set to false. If skip-request is set to false,
             * we generate requests even for methods not in the default list.
             */
            if ($controller && (
                (in_array($methodName, $this->methodsForGenerate) && $skipRequest !== true) ||
                $skipRequest === false
            )
            ) {
                $this->extractedRouteController = RouteControllerResolver::extract($controller);
                $this->generateRequestForOperation($operation);
            }
        }
    }

    public function generateRequestForOperation(Operation $operation): void
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

    public function replaceRules(Operation $operation, string $stubContent): string
    {
        $rules = '';
        if (isset($operation->requestBody->content)) {
            $rules = $this->getAllRules($operation);
        }

        return str_replace('{{ rules }}', $rules, $stubContent);
    }

    public function replaceNamespace(string $stubContent): string
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

    public function getAllRules(Operation $operation): string
    {
        $openapiSchema = $operation->requestBody->content[MediaType::APPLICATION_JSON]->getSerializableData()->schema;
        $schema = (new ModelSchemaParser($openapiSchema))->parse();

        $rules = '';

        foreach ($schema->getProperties() as $propertyName => $property) {
            $rules .= sprintf("\t\t\t'%s' => %s", $propertyName, $this->makeRulesForProperty($property));

            if ($property->originalType === 'object') {
                foreach ($property->getProperties() as $subPropName => $subProperty) {
                    $rules .= sprintf(
                        "\t\t\t'%s' => %s",
                        ($propertyName.'.'.$subPropName),
                        $this->makeRulesForProperty($subProperty)
                    );
                }
            }
        }

        return trim($rules);
    }

    public function makeRulesForProperty(OpenapiProperty $property): string
    {
        $rules = array_map(function ($item) {
            return "'".$item."'";
        }, $property->getLaravelValidationRules());

        return '['.implode(', ', $rules)."],\n";
    }

    public function requestFileExists(): bool
    {
        return file_exists($this->getFilePath());
    }

    public function createRequestFileIfNotExists(): string
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

    public function makeNamespace(): string
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

    public function getFilePath(): string
    {
        $requestFilePath = lcfirst($this->makeNamespace()).'.php';

        return normalizePathSeparators(base_path($requestFilePath));
    }

    public function getRequestStubContent(): string
    {
        return Stub::getStubContent('request.stub');
    }
}
