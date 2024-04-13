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

        if (! empty($options['required'])) {
            $property->required = true;
            $property->addValidationRule('required');
        }

        if (! empty($options['nullable'])) {
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
            $property->addValidationRule($openapiToLaravelValidationMapper->getWithRule(
                'pattern',
                sprintf('/%s/', $property->pattern)
            ));
        }

        if (isset($options['enum'])) {
            $property->isEnum = true;
            $property->enumData = $options['enum'];
        }

        switch (strtolower($options['type'])) {
            case 'string':
                if (isset($options['minLength'])) {
                    $property->minLength = $options['minLength'];
                    $property->addValidationRule(
                        $openapiToLaravelValidationMapper->getWithRule('minLength', $property->minLength)
                    );
                }
                if (isset($options['maxLength'])) {
                    $property->maxLength = $options['maxLength'];
                    $property->addValidationRule(
                        $openapiToLaravelValidationMapper->getWithRule('maxLength', $property->maxLength)
                    );
                }
                break;
            case 'integer':
            case 'number':
                if (isset($options['minimum'])) {
                    $property->minimum = $options['minimum'];
                    $property->addValidationRule(
                        $openapiToLaravelValidationMapper->getWithRule('minimum', $property->minimum)
                    );
                }
                if (isset($options['maximum'])) {
                    $property->maximum = $options['maximum'];
                    $property->addValidationRule(
                        $openapiToLaravelValidationMapper->getWithRule('maximum', $property->maximum)
                    );
                }
                break;
            case 'array':
                if (isset($options['minItems'])) {
                    $property->minimum = $options['minItems'];
                    $property->addValidationRule(
                        $openapiToLaravelValidationMapper->getWithRule('minItems', $property->minimum)
                    );
                }
                if (isset($options['maxItems'])) {
                    $property->maximum = $options['maxItems'];
                    $property->addValidationRule(
                        $openapiToLaravelValidationMapper->getWithRule('maxItems', $property->maximum)
                    );
                }
                break;
            case 'object':
                if (isset($options['minProperties'])) {
                    $property->minProperties = $options['minProperties'];
                    $property->addValidationRule(
                        $openapiToLaravelValidationMapper->getWithRule('minProperties', $property->minProperties)
                    );
                }
                if (isset($options['maxProperties'])) {
                    $property->maxProperties = $options['maxProperties'];
                    $property->addValidationRule(
                        $openapiToLaravelValidationMapper->getWithRule('maxProperties', $property->maxProperties)
                    );
                }
                break;
        }

        return $property;
    }
}
