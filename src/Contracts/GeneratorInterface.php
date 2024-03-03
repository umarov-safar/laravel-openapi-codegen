<?php

namespace Openapi\ServerGenerator\Contracts;

use cebe\openapi\SpecObjectInterface;

interface GeneratorInterface
{
    public function generate(SpecObjectInterface $object): void;
}
