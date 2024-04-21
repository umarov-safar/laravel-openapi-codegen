<?php

namespace LaravelOpenapi\Codegen\DTO;

class Schema
{
    /** @var array<string, OpenapiProperty> */
    protected array $properties = [];

    protected array $required = [];

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addProperty(OpenapiProperty $property): void
    {
        $this->properties[$property->name] = $property;
    }

    public function getProperty(string $propertyName): ?OpenapiProperty
    {
        return $this->properties[$propertyName] ?? null;
    }
}
