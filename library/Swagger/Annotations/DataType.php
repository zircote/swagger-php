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

    /**
     * Undocumented
     * @var mixed
     */
    public $defaultValue;

    protected static $mapAnnotations = array(
        '\Swagger\Annotations\Items' => 'items'
    );

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

    public function validate()
    {
        if (is_string($this->required)) {
            $required = filter_var($this->required, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($required === null) {
                Logger::notice('Invalid `required="'.$this->required.'"` for '.$this->identity().' expecting `required=true`');
            } else {
                $this->required = $required;
            }
        }
        Items::validateContainer($this);
        return true;
    }

    public function identity() {
       $array = explode('\\', get_class($this));
       return '@SWG\\'.array_pop($array).'(name="'.$this->name.'")';
    }


}