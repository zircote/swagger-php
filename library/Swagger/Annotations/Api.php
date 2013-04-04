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
use Swagger\Annotations\Operations;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Api extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $path;
    /**
     * @var string|Operation
     */
    public $operations = array();

    /**
     * @var string
     */
    public $description;

    protected function setNestedAnnotations($annotations) {
        foreach ($annotations as $annotation) {
			if ($annotation instanceof Operation) {
				$this->operations[] = $annotation;
			}
			if ($annotation instanceof Operations) {
				$operations = is_array($annotation->value) ? $annotation->value : array($annotation->value);
				$this->setNestedAnnotations($operations);
			}
		}
    }

	public function validate() {
		$operations = array();
		foreach ($this->operations as $operation) {
			if ($operation->validate()) {
				$operations[] = $operation;
			}
		}
		$this->operations = $operations;
		if (count($this->operations) == 0) {
			Logger::log(new AnnotationException('Api "'.$this->path.'" doesn\'t have any valid operations'));
			return false;
		}
		return true;
	}
}
