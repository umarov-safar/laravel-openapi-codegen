<?php

namespace LaravelOpenapi\Codegen\Utils;

use LaravelOpenapi\Codegen\Data\OpenapiToLaravelValidationMapper;
use LaravelOpenapi\Codegen\DTO\OpenapiProperty;

class OpenapiPropertyConvertor
{
    public static function convert(string $propertyName, array $options): OpenapiProperty
    {
        $property = new OpenapiProperty($propertyName);
        $openapiToLaravelValidationMapper = new OpenapiToLaravelValidationMapper();

        if (isset($options['required'])) {
            $property->required = true;
            $property->addValidationRule('required');
        }

        if (isset($options['nullable'])) {
            $property->nullable = true;
            $property->addValidationRule('nullable');
        }

        $property->type = $options['type'] == 'object' ? 'array' : $options['type'];
        $property->addValidationRule($property->type);

        if (isset($options['format'])) {
            $property->format = $options['format'];

            $formatLaravel = $openapiToLaravelValidationMapper->get($options['format']);
            if ($formatLaravel) {
                $property->addValidationRule($formatLaravel);
            }
        }

        if (isset($options['pattern'])) {
            $property->pattern = $options['pattern'];
        }

        if (isset($options['enum'])) {
            $property->isEnum = true;
            $property->enumData = $options['enum'];
        }

        switch (strtolower($options['type'])) {
            case 'string':
                if (isset($options['minLength'])) {
                    $property->minLength = $options['minLength'];
                    $property->addValidationRule($openapiToLaravelValidationMapper->get($options['minLength']).':'.$property->minLength);
                }
                if (isset($options['maxLength'])) {
                    $property->maxLength = $options['maxLength'];
                    $property->addValidationRule($openapiToLaravelValidationMapper->get($options['maxLength']).':'.$property->minLength);
                }
                break;
            case 'integer':
            case 'number':
                if (isset($options['minimum'])) {
                    $property->minimum = $options['minimum'];
                }
                if (isset($options['maximum'])) {
                    $property->maximum = $options['maximum'];
                }
                break;
            case 'array':
                if (isset($options['minItems'])) {
                    $property->minimum = $options['minItems'];
                }
                if (isset($options['maxItems'])) {
                    $property->maximum = $options['maxItems'];
                }
                break;
            case 'object':
                if (isset($options['minProperties'])) {
                    $property->minProperties = $options['minProperties'];
                }
                if (isset($options['maxProperties'])) {
                    $property->maxProperties = $options['maxProperties'];
                }
                break;
        }

        return $property;
    }

    public function convertMany()
    {

    }
}
