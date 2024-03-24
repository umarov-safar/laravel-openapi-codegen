<?php

namespace Openapi\ServerGenerator\Utils;

use cebe\openapi\spec\MediaType;
use Openapi\ServerGenerator\DTO\Schema;
use stdClass;

class ModelSchemaParser
{
    protected Schema $schema;

    protected stdClass|MediaType $openapiSchema;

    public function __construct(stdClass|MediaType $openapiSchema)
    {
        $this->openapiSchema = $openapiSchema;
        $this->schema = new Schema();
    }

    public function parse(): Schema
    {
        $schema = $this->openapiSchema;

        if ($this->hasOneOf()) {
            return $this->parseTypeOf($this->openapiSchema->oneOf);
        }

        if ($this->hasAllOf()) {
            return $this->parseTypeOf($this->openapiSchema->allOf);
        }

        if ($this->hasAnyOf()) {
            return $this->parseTypeOf($this->openapiSchema->anyOf);
        }

        return $this->parseSchema($schema);
    }

    public function parseTypeOf(array $openapiSchemas): Schema
    {
        foreach ($openapiSchemas as $openapiSchema) {
            $this->parseSchema($openapiSchema);
        }

        return $this->schema;
    }

    public function parseSchema(stdClass $openapiSchema): Schema
    {
        if (isset($openapiSchema->required) && is_array($openapiSchema->required)) {
            foreach ($openapiSchema->required as $propName) {
                $this->schema->pushRequiredPropName($propName);
            }
        }

        if (isset($openapiSchema->type) && $openapiSchema->type == 'object' && isset($openapiSchema->properties)) {
            $properties = get_object_vars($openapiSchema->properties);

            foreach ($properties as $propertyName => $options) {
                $property = OpenapiPropertyConvertor::convert($propertyName, get_object_vars($options));
                $this->schema->addProperty($property);
            }
        }

        return $this->schema;
    }

    public function hasOneOf(): bool
    {
        return isset($this->openapiSchema->oneOf);
    }

    public function hasAnyOf(): bool
    {
        return isset($this->openapiSchema->anyOf);
    }

    public function hasAllOf(): bool
    {
        return isset($this->openapiSchema->allOf);
    }
}
