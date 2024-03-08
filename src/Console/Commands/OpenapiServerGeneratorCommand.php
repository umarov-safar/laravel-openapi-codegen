<?php

namespace Openapi\ServerGenerator\Console\Commands;

use cebe\openapi\Reader;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Config;
use Openapi\ServerGenerator\Factories\DefaultGeneratorFactory;
use Openapi\ServerGenerator\Generators\BaseGenerator;

class OpenapiServerGeneratorCommand extends GeneratorCommand
{
    protected $signature = 'openapi:generate-server';

    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs';
    }

    public function handle(): void
    {
        $spec = Reader::readFromYamlFile(Config::get('openapi-generator.path'));

        foreach (Config::get('openapi-generator.entities') as $entity) {
            $generator = new BaseGenerator(DefaultGeneratorFactory::createGenerator($entity));
            $generator->generate($spec);
        }

    }
}
