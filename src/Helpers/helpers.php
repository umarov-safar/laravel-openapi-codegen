<?php

use cebe\openapi\spec\MediaType;
use Openapi\ServerGenerator\DTO\Schema;
use Openapi\ServerGenerator\Utils\OpenapiPropertyConvertor;

if (! function_exists('normalizePathSeparators')) {
    function normalizePathSeparators(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}


if (! function_exists('type_of')) {
    function type_of(array $openapiSchemas): Schema {
        $schema = new Schema();

        foreach ($openapiSchemas as $item) {
            if (isset($item->required) && is_array($item->required)) {
                foreach ($item->required as $propName) {
                    $schema->pushRequiredPropName($propName);
                }
                continue;
            }

            if (isset($item->type) && $item->type == 'object' && isset($item->properties)) {
                $properties = get_object_vars($item->properties);

                foreach ($properties as $propertyName => $options) {
                    $property = OpenapiPropertyConvertor::convert($propertyName, get_object_vars($options));
                    $schema->pushProperty($property);
                }
            }
        }

        return $schema;
    }
}