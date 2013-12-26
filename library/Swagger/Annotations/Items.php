<?php

namespace Swagger\Annotations;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2013] [Robert Allen]
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
use Swagger\Swagger;
use Swagger\Logger;

//
// Usage:
// items="$ref:Pet"
// or
// @SWG\Items("Pet")
// or
// @SWG\Items(type="Pet")
//

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Items extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $type;

    protected function setNestedValue($value)
    {
        $this->type = $value;
    }

    public function jsonSerialize()
    {
        if (Swagger::isPrimitive($this->type)) {
            return parent::jsonSerialize();
        }
        return array(
            '$ref' => $this->type
        );
    }

    /**
     *
     * @param Property|Parameter|Operation $annotation
     * @return bool
     */
    public static function validateContainer($annotation)
    {
        // Interpret `items="$ref:Model"` as `@SWG\Items(type="Model")`
        if (is_string($annotation->items) && preg_match('/\$ref:(\w+)/', $annotation->items, $matches)) {
            $annotation->items = new Items();
            $annotation->items->type = array_pop($matches);
        }
        // Validate if items are inside a container type.
        if ($annotation->items !== null) {
            if ($annotation->type !== 'array') {
                Logger::warning('Unexcepted items for type "'.$annotation->type.'" in '.$annotation->identity().', expecting "array"');
                $annotation->items = null;
            } else {
                Swagger::checkDataType($annotation->items->type);
            }
        }
        return true;
    }
}
