<?php

namespace LaravelOpenapi\Codegen\DTO;

class ExtractedRouteController
{
    public function __construct(
        public string $namespace,
        public string $controller,
        public string $action,
    ) {
    }

    public static function create(
        string $namespace,
        string $controller,
        string $action,
    ): self {
        return new static($namespace, $controller, $action);
    }
}
