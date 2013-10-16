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
use Swagger\Annotations\Properties;
use Swagger\Logger;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Model extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $properties = array();

    /**
     * @var array
     */
    public $required;

    /**
     * The PHP class connected to this model
     * @var null|string
     */
    public $phpClass;

    /**
     * The superclass connected to this model.
     * @var null|string
     */
    public $phpExtends;

    protected static $mapAnnotations = array(
        '\Swagger\Annotations\Property' => 'properties[]'
    );

    public function __construct(array $values = array()) {
        parent::__construct($values);
        if (is_string($this->required)) {
            $this->required = $this->decode($this->required);
        }
    }

    public function setNestedAnnotations($annotations)
    {
        foreach ($annotations as $index => $annotation) {
            if ($annotation instanceof Properties) {
                foreach ($annotation->properties as $property) {
                    $this->properties[] = $property;
                }
                unset($annotations[$index]);
            }
        }
        return parent::setNestedAnnotations($annotations);
    }

    public function validate()
    {
        $properties = array();
        $required = $this->required ?: array();
        foreach ($this->properties as $property) {
            if ($property->validate()) {
                $properties[] = $property;
                if ($property->required) {
                    $required[] = $property->name;
                }
            }
        }
        $this->properties = $properties;
        if (count($required) > 0) {
            $this->required = array_unique($required);
        }
        return true;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize($this);
        unset($data['phpClass'], $data['phpExtends']);
        if (empty($data['required'])) {
            unset($data['required']);
        }
        $data['properties'] = array();
        foreach ($this->properties as $property) {
            $data['properties'][$property->name] = $property->jsonSerialize();
        }
        return $data;
    }

    public function identity() {
        return '@SWG\Model(id="'.$this->id.'")';
    }
}
