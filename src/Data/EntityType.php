<?php

namespace LaravelOpenapi\Codegen\Data;

class EntityType implements Enumerable
{
    const ROUTE = 'route';

    const CONTROLLER = 'controller';

    const REQUEST = 'request';

    const RESOURCE = 'resource';

    public static function case(): array
    {
        return [
            self::ROUTE,
            self::REQUEST,
            self::CONTROLLER,
            self::RESOURCE,
        ];
    }
}
