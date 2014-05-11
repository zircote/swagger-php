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
use Swagger\Annotations\AbstractAnnotation;
use Swagger\Annotations\Properties;

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

    protected static $mapAnnotations = array(
        '\Swagger\Annotations\Property' => 'properties[]'
    );

    public function __construct(array $values = array())
    {
        parent::__construct($values);
        // required accepts both json syntax and comma-separated syntax.
        if (is_string($this->required)) {
            if (strpos($this->required, '[') !== false) {
                $this->required = $this->decode($this->required);
            } else {
                $this->required = preg_split('/(\s)*,(\s)*/', $this->required);
            }
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
                if ($property->required && in_array($property->name, $required) === false) {
                    $required[] = $property->name;
                }
                $property->required = null;
            }
        }
        $this->properties = $properties;
        sort($required);
        $this->required = $required;
        if (count($required) === 0) {
            $this->required = null;
        }
        return true;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize($this);
        $data['properties'] = array();
        foreach ($this->properties as $property) {
            $data['properties'][$property->name] = $property->jsonSerialize();
        }
        return $data;
    }

    public function identity()
    {
        return '@SWG\Model(id="'.$this->id.'")';
    }
}
