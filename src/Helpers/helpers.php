<?php

if (! function_exists('normalizePathSeparators')) {
    function normalizePathSeparators(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}

if (! function_exists('convertTypeOfSchemaToArray')) {
    function convertTypeOfSchemaToArray($options): array
    {
        $result = [];
        // Assuming $schema is an associative array representing the JSON Schema
        foreach ($options as $key => $value) {
            if ($key === 'oneOf' || $key === 'anyOf' || $key === 'allOf') {
                foreach ($value as $schema) {
                    $result = array_merge($result, convertTypeOfSchemaToArray(get_object_vars($schema)));
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
