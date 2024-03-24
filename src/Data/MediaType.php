<?php

namespace Openapi\ServerGenerator\Data;

class MediaType implements Enumerable
{
    const APPLICATION_JSON = 'application/json';

    const TEXT_PLAIN = 'text/plain';

    const VND_GITHUB_JSON = 'application/vnd.github+json';

    const VND_GITHUB_V3_JSON = 'application/vnd.github.v3+json';

    public static function case(): array
    {
        return [
            self::APPLICATION_JSON,
            self::TEXT_PLAIN,
            self::VND_GITHUB_JSON,
            self::VND_GITHUB_V3_JSON
        ];
    }
}