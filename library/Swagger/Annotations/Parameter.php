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
use Swagger\Swagger;
use Swagger\Logger;
/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Parameter extends AbstractAnnotation
{
	/**
	 * The name of the parameter name
     * @var string
     */
    public $name;
    /**
	 * Description of the parameter
     * @var string
     */
    public $description;
    /**
     * @var bool
     */
    public $allowMultiple = null;
    /**
	 * The dataType of the parameter
     * @var string
     */
    public $dataType;

    /**
	 * "path" is for when the parameter is part of the URL path (e.g /foo/{id}.xml)
	 * "query" is for when the parameter is part of the query_string or a form
     * @var string
     */
    public $paramType;

    /**
	 * If not present defaults to false.
     * @var bool
     */
    public $required;

	/**
	 * @var AllowableValues
	 */
	public $allowableValues;

    /**
     * @var mixed
     */
    public $defaultValue;

	public function __construct(array $values = array()) {
		parent::__construct($values);
		Swagger::checkDataType($this->dataType);
		if ($this->paramType && !in_array($this->paramType, array('path', 'query', 'body', 'header'))) {
			Logger::warning('Unexpected paramType "'.$this->paramType.'", expecting "path", "query", "body" or "header" in '.AbstractAnnotation::$context);
		}
	}

	protected function setNestedAnnotations($annotations) {
		foreach ($annotations as $annotation) {
			if ($annotation instanceof AllowableValues) {
				$this->allowableValues = $annotation;
			} else {
				Logger::notice('Unexpected '.get_class($annotation).' in a '.get_class($this).' in '.AbstractAnnotation::$context);
			}
		}
	}
    /**
     * @return array
     */
    public function toArray()
    {
        $members =  array_filter((array) $this, array($this, 'arrayFilter'));
        $result = array();
        foreach ($members as $k => $m) {
            if ($m instanceof AllowableValues) {
                $members['allowableValues'] = $m->toArray();
            }
        }
        if (isset($members['value'])) {
            foreach ($members['value'] as $k => $m) {
                if ($m instanceof AbstractAnnotation) {
                    $result[] = $m->toArray();
                }
            }
        }
        if (isset($members['value'])) {
            $members['value'] = $result;
        }
        return $members;
    }
}

