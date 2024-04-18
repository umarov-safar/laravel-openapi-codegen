<?php

namespace LaravelOpenapi\Codegen\Generators;

trait ApplyFileCreatable
{
    public function createFileIfNotExist(): string
    {
        $filePath = base_path($this->namespaceInfo->filePath);
        $directory = dirname($filePath);

        if (file_exists($filePath)) {
            return $filePath;
        }

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $newFile = fopen($filePath, 'w');
        fclose($newFile);

        return $filePath;
    }
}
