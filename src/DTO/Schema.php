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

    public function getRequiredProperties(): array
    {
        return $this->required;
    }

    public function addProperty(OpenapiProperty $property): void
    {
        $this->properties[$property->name] = $property;
    }

    public function getProperty(string $propertyName): ?OpenapiProperty
    {
        return $this->properties[$propertyName] ?? null;
    }

    public function addRequiredProperty(string $name): void
    {
        if (! in_array($name, $this->required)) {
            $this->required[] = $name;
        }
    }
}
