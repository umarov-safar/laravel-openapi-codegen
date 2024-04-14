<?php

namespace LaravelOpenapi\Codegen\DTO;

class OpenapiProperty
{
    /**
     * Property name
     */
    public string $name;

    /**
     * Openapi original type
     */
    public string $originalType;

    /**
     * It contains a php type that converted from openapi
     */
    public string $type;

    public bool $nullable = false;

    public ?string $pattern;

    public ?bool $required = false;

    public ?string $format = null;

    public ?int $minLength = null;

    public ?int $maxLength = null;

    public ?int $minimum = null;

    public ?int $maximum = null;

    public bool $isEnum = false;

    public ?array $enumData = [];

    public ?int $minProperties = null;

    public ?int $maxProperties = null;

    /**
     * Contains all OpenApi property validation converted to laravel validations
     */
    protected array $laravelValidation = [];

    /**
     * This contains all properties of OpenApi property which type is object
     */
    protected array $properties = [];

    /**
     * The required properties of Openapi property type object
     */
    protected array $requiredProperties = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getLaravelValidationRules(): array
    {
        return $this->laravelValidation;
    }

    public function addValidationRule(string $rule): void
    {
        if (! in_array($rule, $this->laravelValidation)) {
            $this->laravelValidation[] = $rule;
        }
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addProperty(OpenapiProperty $property): void
    {
        if (array_key_exists($property->name, $this->properties)) {
            return;
        }

        if (in_array($property->name, $this->getRequiredProperties())) {
            $property->required = true;
            $property->addValidationRule('required');
        }

        $this->properties[$property->name] = $property;
    }

    public function getRequiredProperties(): array
    {
        return $this->requiredProperties;
    }

    public function setRequiredProperties(array $required): void
    {
        $this->requiredProperties = $required;
    }
}
