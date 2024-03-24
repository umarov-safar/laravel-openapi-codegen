<?php

namespace Openapi\ServerGenerator\Data;

use Illuminate\Support\Fluent;

class OpenapiToLaravelValidationMapper extends Fluent
{

    protected $attributes = [
        'string' => 'string',
        'minimum' => 'min',
        'maximum' => 'max',
        'minLength' => 'min',
        'maxLength' => 'max',
        'minItems' => 'min',
        'maxItems' => 'max',
        'minProperties' => 'min',
        'maxProperties' => 'max',
//       'uniqueItems' => 'distinct'
        'float' => 'numeric',
        'double' => 'numeric',
        'int' => 'integer',
        'int32' => 'integer',
        'int64' => 'integer',
        'date' => 'date',
        'date-time' => 'date_format:Y-m-d H:i:s',
        'password' => 'string',
        'email' => 'email',
        'uuid' => 'uuid'
    ];
}