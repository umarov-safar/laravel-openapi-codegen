<?php

namespace LaravelOpenapi\Codegen\DTO;

class RouteConfiguration
{
    public function __construct(
        public string $method,
        public string $uri,
        public ?string $routeName,
        public ?string $middlewares
    ) {
    }

    public static function create(
        string $method,
        string $uri,
        ?string $routeName,
        ?string $middlewares
    ): self
    {
        return new static($method, $uri, $routeName, $middlewares);
    }
}
