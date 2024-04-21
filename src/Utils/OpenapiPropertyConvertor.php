<?php

namespace LaravelOpenapi\Codegen\Utils;

use LaravelOpenapi\Codegen\Data\OpenapiToLaravelValidationMapper;
use LaravelOpenapi\Codegen\DTO\OpenapiProperty;

class OpenapiPropertyConvertor
{
    protected static OpenapiToLaravelValidationMapper $openapiToLaravelValidationMapper;

    public static function convert(string $propertyName, array $options): OpenapiProperty
    {
        self::$openapiToLaravelValidationMapper = new OpenapiToLaravelValidationMapper();

        $property = new OpenapiProperty($propertyName);

        if (! empty($options['required'])) {
            $property->required = true;
            $property->addValidationRule('required');
        }

        if (! empty($options['nullable'])) {
            $property->nullable = true;
            $property->addValidationRule('nullable');
        }

        $property->originalType = $options['type'];
        $property->type = $options['type'] == 'object' ? 'array' : $options['type'];
        $property->addValidationRule($property->type);

        if (isset($options['format'])) {
            $property->format = $options['format'];
            $formatLaravel = self::$openapiToLaravelValidationMapper->get($options['format']);
            if ($formatLaravel) {
                $property->addValidationRule($formatLaravel);
            }
        }

        if (isset($options['pattern'])) {
            $property->pattern = $options['pattern'];
            $property->addValidationRule(self::$openapiToLaravelValidationMapper->getWithRule(
                'pattern',
                sprintf('/%s/', $property->pattern)
            ));
        }

        if (isset($options['enum'])) {
            $property->isEnum = true;
            $property->enumData = $options['enum'];
        }

        self::addValidationForType($property, $options);

        return $property;
    }

    private static function addValidationForType(OpenapiProperty $property, array $options): void
    {
        switch (strtolower($options['type'])) {
            case 'string':
                if (isset($options['minLength'])) {
                    $property->minLength = $options['minLength'];
                    $property->addValidationRule(
                        self::$openapiToLaravelValidationMapper->getWithRule('minLength', $property->minLength)
                    );
                }
                if (isset($options['maxLength'])) {
                    $property->maxLength = $options['maxLength'];
                    $property->addValidationRule(
                        self::$openapiToLaravelValidationMapper->getWithRule('maxLength', $property->maxLength)
                    );
                }
                break;
            case 'integer':
            case 'number':
                if (isset($options['minimum'])) {
                    $property->minimum = $options['minimum'];
                    $property->addValidationRule(
                        self::$openapiToLaravelValidationMapper->getWithRule('minimum', $property->minimum)
                    );
                }
                if (isset($options['maximum'])) {
                    $property->maximum = $options['maximum'];
                    $property->addValidationRule(
                        self::$openapiToLaravelValidationMapper->getWithRule('maximum', $property->maximum)
                    );
                }
                break;
            case 'array':
                if (isset($options['minItems'])) {
                    $property->minimum = $options['minItems'];
                    $property->addValidationRule(
                        self::$openapiToLaravelValidationMapper->getWithRule('minItems', $property->minimum)
                    );
                }
                if (isset($options['maxItems'])) {
                    $property->maximum = $options['maxItems'];
                    $property->addValidationRule(
                        self::$openapiToLaravelValidationMapper->getWithRule('maxItems', $property->maximum)
                    );
                }

                if (isset($options['items']) && is_array($options['items'])) {
                    $property->setItems($options['items']);

                }
                break;
            case 'object':
                if (isset($options['minProperties'])) {
                    $property->minProperties = $options['minProperties'];
                    $property->addValidationRule(
                        self::$openapiToLaravelValidationMapper->getWithRule('minProperties', $property->minProperties)
                    );
                }
                if (isset($options['maxProperties'])) {
                    $property->maxProperties = $options['maxProperties'];
                    $property->addValidationRule(
                        self::$openapiToLaravelValidationMapper->getWithRule('maxProperties', $property->maxProperties)
                    );
                }
                if (! empty($options['required']) && is_array($options['required'])) {
                    $property->setRequiredProperties($options['required']);
                }

                if (! empty($options['properties']) && is_array($options['properties'])) {
                    foreach ($options['properties'] as $subKey => $subProperty) {
                        $property->addProperty(
                            OpenapiPropertyConvertor::convert($subKey, $subProperty)
                        );
                    }
                }
                break;
        }
    }
}
