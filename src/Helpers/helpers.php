<?php

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\Schema;
use LaravelOpenapi\Codegen\Data\MediaType;

if (! function_exists('normalizePathSeparators')) {
    function normalizePathSeparators(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}

if (! function_exists('getAllowedResponseContentTypeMedia')) {
    function getAllowedResponseContentTypeMedia(Operation $operation): ?Schema
    {
        $media = $operation->requestBody->content[MediaType::APPLICATION_JSON] ?? null;

        return $media?->schema;
    }
}

if (! function_exists('convertTypeOfSchemaToArray')) {
    function mergeRecursiveTypeOfSchemaPropertiesToArray($properties): array
    {
        foreach ($properties as $key => $property) {
            if (($key === 'oneOf' || $key === 'anyOf' || $key === 'allOf') && is_array($property)) {
                $mergedSubSchemaProperties = [];
                $mergedSchema = [];
                foreach ($property as $schema) {
                    if (is_object($schema) && isset($schema->properties)) {
                        $schema = get_object_vars($schema);
                        $props = mergeRecursiveTypeOfSchemaPropertiesToArray(get_object_vars($schema['properties']));
                        $mergedSubSchemaProperties = array_merge($props, $mergedSubSchemaProperties);
                        $mergedSchema = array_merge($schema, $mergedSchema);
                        $mergedSchema['properties'] = $mergedSubSchemaProperties;
                    } elseif (is_array($property)) {
                        foreach ($property as $subProp) {
                            if (! is_object($subProp)) {
                                return [];
                            }
                            $props = mergeRecursiveTypeOfSchemaPropertiesToArray(get_object_vars($subProp));
                            $mergedSubSchemaProperties = array_merge($props, $mergedSubSchemaProperties);
                        }
                        $mergedSchema = $mergedSubSchemaProperties;
                    }
                }

                return $mergedSchema;
            } elseif (is_object($property)) {
                $propertyArr = get_object_vars($property);
                $result = mergeRecursiveTypeOfSchemaPropertiesToArray($propertyArr);
                $properties[$key] = $result ?: $propertyArr;
            }
        }

        return $properties;
    }
}
