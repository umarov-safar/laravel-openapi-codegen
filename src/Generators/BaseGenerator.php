<?php

namespace Openapi\ServerGenerator\Generators;

use cebe\openapi\SpecObjectInterface;
use Openapi\ServerGenerator\Contracts\GeneratorInterface;

final class BaseGenerator
{
    private GeneratorInterface $generator;

    public function __construct(GeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function setGenerator(GeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function generate(SpecObjectInterface $spec)
    {
        $this->generator->generate($spec);
    }
}