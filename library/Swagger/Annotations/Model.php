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

    protected function setNestedAnnotations($annotations)
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Property) {
                $this->properties[] = $annotation;
            } elseif ($annotation instanceof Properties) {
                foreach ($annotation->properties as $property) {
                    $this->properties[] = $property;
                }
            } else {
                Logger::notice('Unexpected '.get_class($annotation).' in a '.get_class($this).' in '.AbstractAnnotation::$context);
            }
        }
    }

    public function validate()
    {
        $properties = array();
        foreach ($this->properties as $property) {
            if ($property->validate()) {
                $properties[] = $property;
            }
        }
        $this->properties = $properties;
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
            $data['properties'][$property->name] = $property;
        }
        return $data;
    }
}
