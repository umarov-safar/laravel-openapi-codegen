<?php

if (! function_exists('normalizePathSeparators')) {
    function normalizePathSeparators(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
