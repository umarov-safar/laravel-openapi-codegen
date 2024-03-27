<?php

namespace Openapi\ServerGenerator\Console\Commands;

use cebe\openapi\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Openapi\ServerGenerator\Factories\DefaultGeneratorFactory;
use Openapi\ServerGenerator\Generators\BaseGenerator;

class OpenapiServerGeneratorCommand extends Command
{
    protected $signature = 'openapi:generate-server';

    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs';
    }

    public function handle(): void
    {
        $spec = Reader::readFromYamlFile(Config::get('rest-generator.api_docs_url'));

        foreach (Config::get('rest-generator.entities') as $entity) {
            $generator = new BaseGenerator(DefaultGeneratorFactory::createGenerator($entity));
            $generator->generate($spec);
        }
    }
}
