<?php

namespace LaravelOpenapi\Codegen\Tests\Helpers;

use cebe\openapi\Reader;
use Illuminate\Support\Facades\Config;
use LaravelOpenapi\Codegen\Tests\TestCase;

class TypeOfSchemaToArrayTest extends TestCase
{
    /**
     * The test is for: oneOf, anyOf and anyOf
     * The mergeRecursiveTypeOfSchemaPropertiesToArray function recursively merge all schema (oneOf, anyOf and anyOf)
     * to generate request and resource class that contains all possible properties
     */
    public function test_can_convert_all_type_of_to_array()
    {
        $reader = Reader::readFromYamlFile(Config::get('openapi-codegen.api_docs_url'));

        $path = $reader->paths->getPath('/for-validation');
        $properties = $path->post->requestBody->content['application/json']->schema->getSerializableData();
        $result = mergeRecursiveTypeOfSchemaPropertiesToArray(get_object_vars($properties));

        $this->assertIsArray($result);
    }
}
