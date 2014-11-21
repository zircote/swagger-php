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

class PartialsTest extends SwaggerTestCase
{

    /**
     * Merge properties from partial into target model.
     * @link https://github.com/zircote/swagger-php/issues/177
     */
    function testModel()
    {
        $code = <<<END
/**
 * @SWG\Model(
 *   partial="test_partial",
 *   @SWG\Property(name="first_attribute",type="string"),
 * )
 */

/**
 * @SWG\Model(
 *   id="test_usepartial",
 *   @SWG\Partial("test_partial"),
 *   @SWG\Property(name="extra_attribute",type="string"),
 * )
 */
END;
        $swagger = $this->examineCode($code);
        $this->assertEquals(array(
            'id' => 'test_usepartial',
            'properties' => array(
                'extra_attribute' => array('type' => 'string'),
                'first_attribute' => array('type' => 'string'),
            )
        ), $swagger->export($swagger->models['test_usepartial']));
    }

    /**
     * Partials should not fully inherit
     * @link https://github.com/zircote/swagger-php/issues/178
     */
    function testOperation()
    {
        $code = <<<END
/**
 * @SWG\Operation(
 *   partial="Operation/list",
 *   nickname="index",
 *   method="GET",
 *   type="array"
 * )
 */

/**
 * @SWG\Resource(
 *   resourcePath="/periods",
 *   @SWG\Api(
 *     path="/periods",
 *     @SWG\Operation(
 *       @SWG\Partial("Operation/list"),
 *       @SWG\Items("Period"),
 *     ),
 *   )
 * )
 */
END;
        $swagger = $this->examineCode($code);
        $resource = $swagger->getResource('/periods');
        $this->assertEquals(array(
            'method' => 'GET',
            'nickname' => 'index',
            'type' => 'array',
            'items' => array('$ref' => 'Period')
        ), $resource['apis'][0]['operations'][0]);
    }

}
