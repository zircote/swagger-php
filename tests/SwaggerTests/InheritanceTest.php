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

class InheritanceTest extends SwaggerTestCase
{

    public function testBasicInheritance()
    {
        $code = <<<END
/**
 * @SWG\Model()
 */
class Parent {
    /**
     * @var string
     * @SWG\Property(required=true)
     */
    protected \$name;
}
/**
 * @SWG\Model()
 */
class Child extends Parent {
    /**
     * @var int
     * @SWG\Property(required=true)
     */
    protected \$id;
}
END;
        $swagger = $this->examineCode($code);

        // Assert parser & parent
        $this->assertCount(2, $swagger->models);
        $this->assertCount(1, $swagger->models['Parent']->properties);
        $this->assertEquals('name', $swagger->models['Parent']->properties[0]->name);
        $this->assertContains('name', $swagger->models['Parent']->required);
        $this->assertEquals(array('name'), $swagger->models['Parent']->required);
        // Assert child and inheritance
        $this->assertCount(2, $swagger->models['Child']->properties);
        $this->assertEquals('id', $swagger->models['Child']->properties[0]->name);
        $this->assertEquals('name', $swagger->models['Child']->properties[1]->name);
        $this->assertContains('id', $swagger->models['Child']->required);
        $this->assertEquals(array('id', 'name'), $swagger->models['Child']->required);
    }

    function testPropertyOnlyRequiredInSubclass()
    {
        $code = <<<END
/**
 * Class UserBase
 *
 * @SWG\Model(id="UserBase")
 */
class UserBase {
    /**
     * @SWG\Property()
     */
    public \$email;
}

/**
 * @SWG\Model(id="UserNew",required="['email']")
 */
class UserNew extends UserBase { }

/**
 * @SWG\Model(id="UserUpdate")
 */
class UserUpdate extends UserBase { }
END;
        $swagger = $this->examineCode($code);
        $this->assertCount(1, $swagger->models['UserNew']->required);
        $this->assertNull($swagger->models['UserUpdate']->required);
    }

}
