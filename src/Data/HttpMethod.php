<?php

namespace LaravelOpenapi\Codegen\Data;

class HttpMethod implements Enumerable
{
    const HEAD = 'head';

    const GET = 'get';

    const POST = 'post';

    const PUT = 'put';

    const PATCH = 'patch';

    const DELETE = 'delete';

    const OPTIONS = 'options';

    public static function case(): array
    {
        return [
            self::HEAD,
            self::GET,
            self::POST,
            self::PUT,
            self::PATCH,
            self::DELETE,
            self::OPTIONS,
        ];
    }
}
