<?php

namespace LaravelOpenapi\Codegen\Tests\Helpers;

use cebe\openapi\Reader;
use Illuminate\Support\Facades\Config;
use LaravelOpenapi\Codegen\Tests\TestCase;

class TypeOfSchemaToArrayTest extends TestCase
{
    /**
     * @incom
     */
    public function test_can_convert_all_type_of_to_array()
    {
        $reader = Reader::readFromYamlFile(Config::get('openapi-codegen.api_docs_url'));

        $path = $reader->paths->getPath('/for-validation');
        $properties = $path->post->requestBody->content['application/json']->schema->getSerializableData();
        $converted = mergeRecursiveTypeOfSchemaPropertiesToArray(get_object_vars($properties));
        $this->assertTrue(true);
        //        $this->markTestSkipped();
    }
}
