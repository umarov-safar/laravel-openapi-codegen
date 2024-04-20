<?php

namespace LaravelOpenapi\Codegen\Data;

use Illuminate\Support\Fluent;

class OpenapiTypeToPhpMapper extends Fluent
{
    protected $attributes = [
        'string' => 'string',
        'integer' => 'int',
        'number' => 'int',
        'float' => 'float',
        'boolean' => 'boolean',
        'object' => 'array',
        'array' => 'array',
    ];

    public function get($key, $default = 'mixed'): string
    {
        return parent::get($key, $default);
    }
}
