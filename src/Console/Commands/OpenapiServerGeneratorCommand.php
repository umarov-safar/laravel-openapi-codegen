<?php

namespace Openapi\ServerGenerator\Console\Commands;

use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\Reader;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Config;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;
use Openapi\ServerGenerator\Factories\GeneratorFactory;
use Openapi\ServerGenerator\Generators\BaseGenerator;

class OpenapiServerGeneratorCommand extends GeneratorCommand
{

    protected $signature = 'openapi:generate-server';

    protected function getStub(): string
    {
        return __DIR__ . '/../../../stubs';
    }

    /**
     * @throws UnresolvableReferenceException
     * @throws IOException
     * @throws TypeErrorException
     */
    public function handle(): void
    {
        $spec = Reader::readFromYamlFile(realpath(Config::get('openapi-generator.path')));

        /**
         * @var  $key
         * @var GeneratorInterface $generator
         */
        foreach (Config::get('openapi-generator.entities') as $entity) {
            $generator = new BaseGenerator(GeneratorFactory::createGenerator($entity));
            $generator->generate($spec);
        }

    }

}