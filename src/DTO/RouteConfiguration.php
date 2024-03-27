<?php

namespace LaravelOpenapi\Codegen\DTO;

class RouteConfiguration
{
    public function __construct(
        public ?string $routeName,
        public ?string $middlewares
    ) {
    }

    public static function create(?string $routeName, ?string $middlewares): self
    {
        return new static($routeName, $middlewares);
    }
}
