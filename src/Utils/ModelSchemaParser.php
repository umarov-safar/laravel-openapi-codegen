<?php

namespace LaravelOpenapi\Codegen\Utils;

use LaravelOpenapi\Codegen\DTO\Schema;
use stdClass;

class ModelSchemaParser
{
    protected Schema $schema;

    protected stdClass $openapiSchema;

    public function __construct(stdClass $openapiSchema)
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
        $requiredProperties = $openapiSchema->required ?? [];

        if (isset($openapiSchema->type) && $openapiSchema->type == 'object' && isset($openapiSchema->properties)) {
            $properties = mergeRecursiveTypeOfSchemaPropertiesToArray(get_object_vars($openapiSchema->properties));

            foreach ($properties as $propertyName => $options) {
                if (in_array($propertyName, $requiredProperties)) {
                    $options['inRequired'] = true;
                }
                $property = OpenapiPropertyConvertor::convert($propertyName, $options);
                $this->schema->addProperty($property);
            }
        } elseif (isset($openapiSchema->type) && $openapiSchema->type == 'array' && ! empty($openapiSchema->items)) {
            if (isset($openapiSchema->items->properties)) {
                $properties = mergeRecursiveTypeOfSchemaPropertiesToArray(get_object_vars($openapiSchema->items->properties));

                foreach ($properties as $propertyName => $options) {
                    $property = OpenapiPropertyConvertor::convert($propertyName, $options);
                    $this->schema->addProperty($property);
                }
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
