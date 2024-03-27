<?php

namespace LaravelOpenapi\Codegen\DTO;

class ExtractedRouteController
{
    public function __construct(
        public string $namespace,
        public string $controller,
        public string $action,
        public ?string $httpMethod = null
    ) {
    }

    public static function create(
        string $namespace,
        string $controller,
        string $action,
        ?string $httpMethod = null
    ): self {
        return new static($namespace, $controller, $action, $httpMethod);
    }
}
