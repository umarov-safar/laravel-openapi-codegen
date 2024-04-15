<?php

namespace LaravelOpenapi\Codegen\Console\Commands;

use cebe\openapi\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use LaravelOpenapi\Codegen\Factories\DefaultGeneratorFactory;
use LaravelOpenapi\Codegen\Generators\BaseGenerator;

class LaravelOpenapiCodegenCommand extends Command
{
    protected $signature = 'openapi:generate-code';

    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs';
    }

    public function handle(): void
    {
        $spec = Reader::readFromYamlFile(Config::get('openapi-codegen.api_docs_url'));

        $bar = $this->output->createProgressBar(count($spec->paths));
        $bar->start();

        foreach (Config::get('openapi-codegen.entities') as $entity) {
            $generator = new BaseGenerator(DefaultGeneratorFactory::createGenerator($entity));
            $generator->generate($spec);
            $bar->advance();
        }

        $bar->finish();
    }
}
