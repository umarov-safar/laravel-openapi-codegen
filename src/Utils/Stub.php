<?php

namespace Openapi\ServerGenerator\Utils;

class Stub
{
    public static function getBasePath(): string
    {
        return config('openapi-generator.stubs');
    }

    public static function getStubFilePath(string $fileName): string
    {
        if (self::stubExists($fileName)) {
            return self::getBasePath()."/$fileName";
        }

        throw new \Exception("The stub $fileName file not exists");
    }

    public static function stubExists(string $fileName): bool
    {
        $stubFile = self::getBasePath()."/$fileName";

        if (file_exists($stubFile)) {
            return true;
        }

        return false;
    }

    public static function getStubContent(string $fileName): string
    {
        return file_get_contents(self::getStubFilePath($fileName));
    }
}
