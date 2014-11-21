<?php

namespace SwaggerTests;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2014] [Robert Allen]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   SwaggerTests
 * @package    Swagger
 * @subpackage UnitTests
 */
use Swagger\Swagger;

/**
 *
 *
 * @category   SwaggerTests
 * @package    Swagger
 * @subpackage UnitTests
 * @group Swagger
 */
class FilterResourceTest extends SwaggerTestCase
{

    /**
     * Filter resource list by api version
     */
    public function testResourceFilter()
    {
        $swagger = new Swagger($this->examplesDir('Petstore'));
        $swagger->registry['/pet']->apiVersion = 4; // Set "/pet" to a version below 1

        $before = $swagger->getResourceList();
        $this->assertCount(3, $before['apis'], 'The /pet, /user and /store resources');

        // Filter out all unstable versions
        $swagger->registry = array_filter($swagger->registry, function ($resource) {
            return version_compare($resource->apiVersion, 4, '==');
        });
        $after = $swagger->getResourceList();
        $this->assertCount(1, $after['apis']);
        $this->assertEquals('/pet', $after['apis'][0]['path'], 'Resources /user and /store didn\'t match the filter and only /pet remains');
    }

    /**
     * Swagger before 0.8 implemented \Serializable
     * This is no longed need, which this testcase demonstrates.
     */
    public function testSerializeUnserialize()
    {
        $original = new Swagger($this->examplesDir('Facet'));
        $serialized = serialize($original);
        $swagger = unserialize($serialized);
        $this->assertEquals($original->models, $swagger->models);
        $this->assertEquals($original->registry, $swagger->registry);
        $this->assertOutputEqualsJson('Facet/facet.json', $swagger->getResource('/facet', array('output' => 'json')));
    }

}
