<?php

namespace LaravelOpenapi\Codegen\Tests\Utils;

use cebe\openapi\Reader;
use Illuminate\Support\Facades\Config;
use LaravelOpenapi\Codegen\Data\MediaType;
use LaravelOpenapi\Codegen\Tests\TestCase;
use LaravelOpenapi\Codegen\Utils\ModelSchemaParser;

class ModelSchemaParserTest extends TestCase
{
    public function test_can_parse_model_correctly()
    {

        $reader = Reader::readFromYamlFile(Config::get('openapi-codegen.api_docs_url'));
        $path = $reader->paths->getPath('/for-validation');
        $schema = $path->post->requestBody->content[MediaType::APPLICATION_JSON]->getSerializableData()->schema;

        $schemaParser = new ModelSchemaParser($schema);

    }
}
