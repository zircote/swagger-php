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
use Swagger\Logger;
use Swagger\Swagger;

/**
 * Baseclass for the @SWG\Parameter & @SWG\Property annotations.
 * @link https://github.com/wordnik/swagger-core/wiki/datatypes
 *
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
abstract class DataType extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * The human-readable description.
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $type;

    /**
     *
     * @var string
     */
    public $format;

    /**
     * @var Items
     */
    public $items;

    /**
     * @var bool
     */
    public $uniqueItems;

    /**
     * @var bool
     */
    public $required;

    /**
     *
     * @var mixed
     */
    public $minimum;

    /**
     *
     * @var mixed
     */
    public $maximum;

    /**
     * @var array
     */
    public $enum;

    public function __construct(array $values = array())
    {
        parent::__construct($values);
        Swagger::checkDataType($this->type);
        if (is_string($this->enum)) {
            $values = $this->decode($this->enum);
            if (is_object($values)) {
                $this->enum = array();
                foreach ($values as $key => $value) {
                    $this->enum[] = $key.'-'.$value;
                }
            } else {
                $this->enum = $values;
            }
        }
    }

    public function setNestedAnnotations($annotations)
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Items) {
                $this->items = $annotation;
            } else {
                Logger::notice('Unexpected '.get_class($annotation).' in a '.get_class($this).' in '.AbstractAnnotation::$context);
            }
        }
    }

    public function validate()
    {
        // Interpret `items="$ref:Model"` as `@SWG\Items(type="Model")`
        if (is_string($this->items) && preg_match('/\$ref:(\w+)/', $this->items, $matches)) {
            $this->items = new Items();
            $this->items->type = array_pop($matches);
        }
        // Validate if items are inside a container type.
        if ($this->items !== null) {
            if ($this->type !== 'array') {
                Logger::warning('Unexcepted items for type "'.$this->type.'" in parameter "'.$this->name.'", expecting "array"');
                $this->items = null;
            } else {
                Swagger::checkDataType($this->items->type);
            }
        }
        return true;
    }


}