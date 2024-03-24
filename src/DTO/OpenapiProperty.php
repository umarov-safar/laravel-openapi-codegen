<?php

namespace Openapi\ServerGenerator\DTO;

class OpenapiProperty
{
    public string $name;

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

    protected array $laravelValidation = [];

    public function __construct(
        string $name,
        array $options,
    ) {
        $this->name = $name;
        $this->type = $options['type'];
        $this->addValidationItem($options['type'] === 'object' ? 'array' : $options['type']);
    }

    public function getLaravelValidation(): array
    {
        return $this->laravelValidation;
    }

    public function addValidationItem(string $name): void
    {
        if (! in_array($name, $this->laravelValidation)) {
            $this->laravelValidation[] = $name;
        }
    }
}
