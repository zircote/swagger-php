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
use Swagger\Logger;
use Swagger\Swagger;
use Swagger\Parser;
use Doctrine\Common\Annotations\AnnotationException;
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
     * @var AllowableValues
     */
    public $allowableValues;

    /**
     * @var Items
     */
    public $items;

	public function validate()
	{
		$map = array(
			'array' => 'Array',
			'byte' => 'byte',
			'boolean' => 'boolean',
			'bool' => 'boolean',
			'int' => 'int',
			'integer' => 'int',
			'long' => 'long',
			'float' => 'float',
			'double' => 'double',
			'string' => 'string',
			'date' => 'Date',
			'list' => 'List',
			'set' => 'Set',
		);
		if (array_key_exists(strtolower($this->type), $map)  && array_search($this->type, $map) === false) {
			// Don't correct the type, this creates the incentive to use consistent naming in the doc comments.
			Logger::notice('Encountered type "'.$this->type.'" for '.Parser::$current.', did you mean "'.$map[strtolower($this->type)].'"');
		}
		// Interpret `items="$ref:Model"` as `@SWG\Items(type="Model")`
		if (is_string($this->items) && preg_match('/\$ref:(\w+)/', $this->items, $matches)) {
			$this->items = new Items();
			$this->items->type = array_pop($matches);
		}

		// Validate if items are inside a container type.
		if ($this->items !== null) {
			if (Swagger::isContainer($this->type) === false) {
				Logger::warning(new AnnotationException('Unexcepted items for type "'.$this->type.'" in parameter "'.$this->name.'", expecting type "Array", "List" or "Set"'));
				$this->items = null;
			} elseif (array_key_exists(strtolower($this->items->type), $map) && array_search($this->items->type, $map) === false) {
				// Don't correct the type, this creates the incentive to use consistent naming in the doc comments.
				Logger::notice('Encountered "'.$this->items->type.'" as items->type for property "'.$this->name.'", did you mean "'.$map[strtolower($this->type)].'"');
			}
		}
		return true;
	}

	public function jsonSerialize()
	{
		$data = parent::jsonSerialize();
		unset($data['name']);
		foreach ($data as $key => $value) {
			if ($value === null) {
				unset($data[$key]);
			}
		}
		return $data;
	}
	public function setNestedAnnotations($annotations) {
		foreach ($annotations as $annotation) {
			if ($annotation instanceof AllowableValues) {
				$this->allowableValues = $annotation;
			} elseif ($annotation instanceof Items) {
				$this->items = $annotation;
			}
		}
	}

	public function toArray()
    {
        $result = parent::toArray();
        if (isset($result['items'])) {
            if (preg_match('/\$ref:(\w+)/', $result['items'], $matches)) {
                $result['items'] = array('$ref' => array_pop($matches));
            }
        }
        if (isset($result['type']) && $result['type'] == 'array') {
            $result['type'] = 'Array';
        }
        if (isset($result['value'])) {
            if (isset($result['value']['type'])) {
                $result['items']['type'] = $result['value']['type'];
                unset($result['value']);
            } else {
                $result['allowableValues'] = $result['value'];
                unset($result['value']);
            }
        }
        if (!isset($result['type']) && $this->reflector instanceof \ReflectionProperty &&
            preg_match('/@var\s+(\w+)/i', $this->reflector->getDocComment(), $matches)) {
            $this->type = (string)array_pop($matches);
            $this->name = (string)$this->reflector->name;
            $result = $this->toArray();
        }
        unset($result['reflector']);
        return $result;
    }

}

