<?php

namespace Openapi\ServerGenerator\Helpers;

use Openapi\ServerGenerator\Data\MediaType;
use stdClass;

class SchemaHelper
{
    public static function hasOneOf(stdClass|MediaType $schema)
    {
        return isset($schema->oneOf);
    }

    public static function hasAnyOf(stdClass|MediaType $schema)
    {
        return isset($schema->anyOf);
    }

    public static function hasAllOf(stdClass|MediaType $schema)
    {
        return isset($schema->allOf);
    }
}