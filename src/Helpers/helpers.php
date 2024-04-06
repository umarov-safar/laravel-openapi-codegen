<?php

if (! function_exists('normalizePathSeparators')) {
    function normalizePathSeparators(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}

if (! function_exists('convertTypeOfSchemaToArray')) {
    function mergeRecursiveTypeOfSchemaPropertiesToArray($properties): array
    {
        foreach ($properties as $key => $property) {
            if (($key === 'oneOf' || $key === 'anyOf' || $key === 'allOf') && is_array($property)) {
                $mergedSubSchemaProperties = [];
                $mergedSchema = [];

                foreach ($property as $_key => $schema) {
                    if (is_object($schema) && isset($schema->properties)) {
                        $schema = get_object_vars($schema);
                        $props = mergeRecursiveTypeOfSchemaPropertiesToArray(get_object_vars($schema['properties']));
                        $mergedSubSchemaProperties = array_merge($props, $mergedSubSchemaProperties);
                        $mergedSchema = array_merge($schema, $mergedSchema);
                        $mergedSchema['properties'] = $mergedSubSchemaProperties;
                    } else {
                        $mergedSchema = $property;
                    }
                }

                return $mergedSchema;
            } elseif (is_object($property)) {
                $properties[$key] = mergeRecursiveTypeOfSchemaPropertiesToArray(get_object_vars($property));
            }
        }

        return $properties;
    }
}
