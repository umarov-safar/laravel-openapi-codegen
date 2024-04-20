<?php

namespace LaravelOpenapi\Codegen\Generators;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\SpecObjectInterface;
use Illuminate\Filesystem\Filesystem;
use LaravelOpenapi\Codegen\Contracts\GeneratorInterface;
use LaravelOpenapi\Codegen\Data\OpenapiTypeToPhpMapper;
use LaravelOpenapi\Codegen\DTO\ExtractedRouteController;
use LaravelOpenapi\Codegen\DTO\NamespaceInfo;
use LaravelOpenapi\Codegen\Utils\NamespaceConvertor;
use LaravelOpenapi\Codegen\Utils\RouteControllerResolver;
use LaravelOpenapi\Codegen\Utils\Stub;
use ReflectionClass;

class ControllerGenerator implements GeneratorInterface
{
    use ApplyFileCreatable;

    protected NamespaceInfo $namespaceInfo;

    protected NamespaceInfo $requestNamespaceInfo;

    protected NamespaceInfo $resourceNamespaceInfo;

    protected Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function generate(SpecObjectInterface $spec): void
    {
        foreach ($spec->paths as $uri => $path) {
            $this->makeController($uri, $path);
        }
    }

    public function makeController(string $uri, PathItem $pathItem)
    {
        foreach ($pathItem->getOperations() as $operation) {
            if (isset($operation->{'l-og-controller'})) {
                $controller = $operation->{'l-og-controller'};
                $this->setNamespacesInfo($controller);
                $this->generateControllerOrAddMethodToControllerFromOperation($uri, $operation);
            }
        }
    }

    public function setNamespacesInfo(string $controller)
    {
        $this->namespaceInfo = NamespaceConvertor::makeNamespaceInfoForController($controller);
        $this->requestNamespaceInfo = NamespaceConvertor::makeRequestNamespace($controller);
        $this->resourceNamespaceInfo = NamespaceConvertor::makeResourceNamespace($controller);
    }

    public function generateControllerOrAddMethodToControllerFromOperation(string $uri, Operation $operation): void
    {
        if ($this->controllerFilExists()) {

            $extractedRouteController = RouteControllerResolver::extract($operation->{'l-og-controller'});

            $class = new ReflectionClass($this->namespaceInfo->namespace);
            if (! $class->hasMethod($extractedRouteController->action)) {
                $controllerNamespace = $this->addNewNamespaces($operation);
                $this->addNewMethodInControllerFileContent($uri, $extractedRouteController, $operation);
            }
        } else {
            $file = $this->createFileIfNotExist();
            $stubContent = $this->replaceNamespaceAndClassName();
            $this->filesystem->put($file, $stubContent);
            $this->generateControllerOrAddMethodToControllerFromOperation($uri, $operation);
        }
    }

    public function addNewNamespaces(Operation $operation)
    {
        $isRequestSkipped = $operation->{'l-og-skip-request'} ?? true;
        $isResourceSkipped = $operation->{'l-og-skip-resource'} ?? true;

        $namespaces = [];
        if (! $isRequestSkipped) {
            $namespaces[] = $this->requestNamespaceInfo->namespace.';';
        }
        if (! $isResourceSkipped) {
            $namespaces[] = $this->resourceNamespaceInfo->namespace.';';
        }

        $controllerFileContent = $this->filesystem->get(base_path($this->namespaceInfo->filePath));

        preg_match("/^\s*\w*\s*class\s+\w+/m", $controllerFileContent, $beforeClassMatches, PREG_OFFSET_CAPTURE);

        $beforeClassPart = trim(substr($controllerFileContent, 0, $beforeClassMatches[0][1]));
        $classPart = trim(substr($controllerFileContent, $beforeClassMatches[0][1]));

        // get all namespaces from file
        preg_match_all("/^\s*use\s*[\w\\\]+;$/m", $beforeClassPart, $namespaceMatches);
        // remove all namespaces in file
        $beforeClassPart = trim(preg_replace("/^\s*use\s*[\w\\\]+;$/m", '', $beforeClassPart));

        $namespaceMatches = array_map(fn ($item) => trim($item), $namespaceMatches[0]);

        // checking new namespace exist in file or not.
        // if namespace was not add in file we add it in list namespaces
        foreach ($namespaces as $newNamespace) {
            $exists = false;
            foreach ($namespaceMatches as $namespaceInFile) {
                if (strpos($namespaceInFile, $newNamespace)) {
                    $exists = true;
                }
            }
            if (! $exists) {
                $namespaceMatches[] = "use $newNamespace";
            }
        }

        // convert namespaces list to string
        $beforeClassPart .= "\n\n".implode("\n", $namespaceMatches)."\n\n";
        $fileContent = $beforeClassPart.$classPart;
        $this->filesystem->put(base_path($this->namespaceInfo->filePath), $fileContent);
    }

    public function addNewMethodInControllerFileContent(string $uri, ExtractedRouteController $extractedRouteController, Operation $operation)
    {
        $methodStub = $this->makeControllerMethod($uri, $extractedRouteController, $operation);
        $controllerFileContent = $this->filesystem->get(base_path($this->namespaceInfo->filePath));
        $controllerFileContentUntilEndClass = substr($controllerFileContent, 0, strrpos($controllerFileContent, '}'));
        $newContent = $controllerFileContentUntilEndClass.$methodStub."\n}";

        $this->filesystem->put(base_path($this->namespaceInfo->filePath), $newContent);
    }

    public function makeControllerMethod($uri, ExtractedRouteController $extractedRouteController, Operation $operation)
    {
        $stubMethod = $this->getControllerMethodStubContent();

        $params = $this->getControllerMethodParams($uri, $operation);
        $resource = $this->getResourceIfNotSkipped($operation);

        return str_replace(
            [
                '{{ method }}',
                '{{ params }}',
                '{{ resource }}',
            ],
            [
                $extractedRouteController->action,
                $params,
                $resource,
            ],
            $stubMethod
        );
    }

    public function getControllerMethodParams(string $uri, Operation $operation): string
    {
        $params = $this->getUrlParamsWithTypesFromParametersOperation($uri, $operation);

        $isRequestSkipped = $operation->{'l-og-skip-request'} ?? true;
        if (! $isRequestSkipped) {
            $params[$this->requestNamespaceInfo->className] = 'request';
        }

        $stringParams = '';
        foreach ($params as $type => $paramName) {
            $stringParams .= $type.' $'.$paramName.', ';
        }

        return trim($stringParams, ', ');
    }

    public function getUrlParamsWithTypesFromParametersOperation(string $uri, Operation $operation)
    {
        $params = $this->getUrlParams($uri);
        if (! empty($params) && isset($operation->parameters) && is_array($operation->parameters)) {
            foreach ($operation->parameters as $parameter) {
                if (isset($parameter->in) && $parameter->in === 'path' && in_array($parameter->name, $params)) {
                    $type = 'mixed';
                    if (isset($parameter->schema->type)) {
                        $type = (new OpenapiTypeToPhpMapper())->get($parameter->schema->type);
                    }
                    $params[$parameter->name] = $type;
                }
            }
            $params = array_flip($params);
        } else {
            $params = [];
        }

        return $params;
    }

    public function getUrlParams(string $uri): array
    {
        $params = [];
        preg_match_all("/\{(\w+)\}/", $uri, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $urlParam) {
                $params[$urlParam] = $urlParam;
            }
        }

        return $params;
    }

    public function replaceNamespaceAndClassName(): string
    {
        $stubContent = $this->getControllerStubContent();

        return str_replace([
            '{{ namespace }}',
            '{{ class }}',
        ],
            [
                $this->namespaceInfo->namespaceWithoutClassName,
                $this->namespaceInfo->className,
            ], $stubContent);
    }

    public function getResourceIfNotSkipped(Operation $operation): string
    {
        $isResourceSkipped = $operation->{'l-og-skip-resource'} ?? true;

        if (! $isResourceSkipped) {
            return sprintf('return %s%s;', $this->resourceNamespaceInfo->className, '()');
        }

        return 'code here';
    }

    public function controllerFilExists(): bool
    {
        return file_exists(base_path($this->namespaceInfo->filePath));
    }

    public function getControllerStubContent()
    {
        return Stub::getStubContent('controller.stub');
    }

    public function getControllerMethodStubContent()
    {
        return Stub::getStubContent('controller.method.stub');
    }
}
