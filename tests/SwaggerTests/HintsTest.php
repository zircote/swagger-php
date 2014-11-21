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

class HintsTest extends SwaggerTestCase
{

    public function testInvalidCommentType()
    {
        $code = <<<END
/*
 * @SWG\Model(id="Participant")
 */
END;
        try {
            $this->examineCode($code);
            $this->fail('Should warn about the annotation in a /* block'); // depends on running phpunit with `convertNoticesToExceptions="true"`
        } catch (\PHPUnit_Framework_Error_Notice $e) {
            $this->assertStringStartsWith('Annotations are only parsed inside `/**` DocBlocks', $e->getMessage());
        }
    }

}
