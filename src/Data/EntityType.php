<?php

namespace Openapi\ServerGenerator\Data;

class EntityType implements Enumerable
{
    const ROUTE = 'route';

    const CONTROLLER = 'controller';

    const REQUEST = 'request';

    public static function case(): array
    {
        return [
            self::ROUTE,
            self::REQUEST,
            self::CONTROLLER,
        ];
    }
}
