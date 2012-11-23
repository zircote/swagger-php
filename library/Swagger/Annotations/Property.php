<?php
namespace Swagger\Annotations;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2012] [Robert Allen]
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
 * @package
 * @category
 * @subpackage
 */
/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Property extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $description;

    /**
     * @var mixed
     */
    public $value;

    /**
     * @var array
     */
    public $items;

    public function toArray()
    {
        $result = parent::toArray();
        if (isset($result['items'])) {
            if (preg_match('/\$ref:(\w+)/', $result['items'], $matches)) {
                $result['items'] = array('$ref' => array_pop($matches));
        }
        }
        if(isset($result['type']) && $result['type'] == 'array'){
            $result['type'] = 'Array';
        }
        if (isset($result['value'])){
            if(isset($result['value']['type'])){
                $result['items']['type'] = $result['value']['type'];
                unset($result['value']);
            } else {
                $result['allowableValues'] = $result['value'];
                unset($result['value']);
            }
        }

        return $result;
    }
}

