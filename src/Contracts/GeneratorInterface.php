<?php

namespace LaravelOpenapi\Codegen\Contracts;

use cebe\openapi\SpecObjectInterface;

interface GeneratorInterface
{
    public function generate(SpecObjectInterface $object): void;
}
