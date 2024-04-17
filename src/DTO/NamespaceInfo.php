<?php

namespace LaravelOpenapi\Codegen\DTO;

class NamespaceInfo
{
    public function __construct(
        public string $className,
        public string $namespace,
        public string $filePath,
        public string $namespaceWithoutClassName
    ) {
    }

    public static function create(
        string $className,
        string $namespace,
        string $filePath,
        string $namespaceWithoutClassName
    ): self {
        return new static($className, $namespace, $filePath, $namespaceWithoutClassName);
    }
}
