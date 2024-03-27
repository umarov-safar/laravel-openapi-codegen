<?php

namespace LaravelOpenapi\Codegen\Tests\Commands;

use LaravelOpenapi\Codegen\Console\Commands\LaravelOpenapiCodegenCommand;
use LaravelOpenapi\Codegen\Tests\TestCase;

class OpenapiServerGeneratorTest extends TestCase
{
    public function test_can_generate_file()
    {
        $this->artisan(LaravelOpenapiCodegenCommand::class)->assertSuccessful();
    }
}
