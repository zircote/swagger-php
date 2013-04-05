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
use Swagger\Annotations\Parameters;
use Swagger\Annotations\ErrorResponses;
use Swagger\Logger;

/**
 * The Operation is what is shown as a bar-like container in the interactive UI.
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Operation extends AbstractAnnotation
{
	/**
	 * The http verb of the operation
     * @var string
     */
    public $httpMethod;

	/**
	 * The summary of the operation, the text that is displayed on the bar-like container,
	 * it is advisable to be short otherwise will not fit.
     * @var string (max 60 characters)
     */
    public $summary;

    /**
	 * The description is displayed once the bar-like container is clicked.
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $nickname;

    /**
     * @var string
     */
    public $responseClass;

    /**
	 * Parameters of the operation.
     * @var array|Parameter
     */
    public $parameters = array();

    /**
	 * ErrorResponses of the operation.
     * @var array|ErrorResponse
     */
    public $errorResponses = array();

    /**
     * @var string
     */
    public $notes;

    /**
     * @var bool
     */
    public $deprecated;

    /**
     * @param array $values
     */
    public function __construct($values)
    {
        parent::__construct($values);
        $this->notes = $this->removePreamble($this->notes);
    }

	protected function setNestedAnnotations($annotations) {
		foreach ($annotations as $annotation) {
			if ($annotation instanceof Parameter) {
				$this->parameters[] = $annotation;
			} elseif ($annotation instanceof Parameters) {
				foreach ($annotation->parameters as $parameter) {
					$this->parameters[] = $parameter;
				}
			} elseif ($annotation instanceof ErrorResponse) {
				$this->errorResponses[] = $annotation;
			} elseif ($annotation instanceof ErrorResponses) {
				foreach ($annotation->errorResponses as $errorResponse) {
					$this->errorResponses[] = $errorResponse;
				}
			} else {
				Logger::notice('Unexpected '.get_class($annotation).' in a '.get_class($this).' in '.AbstractAnnotation::$context);
			}
		}
	}

	public function validate() {
		if (empty($this->nickname)) {
			Logger::notice('The optional field "nickname" is required for the swagger-ui client for an "'.get_class($this).'" in '.AbstractAnnotation::$context);
		}
		return true;
	}

	public function jsonSerialize() {
		$data = parent::jsonSerialize();
		if (count($this->errorResponses) === 0) {
			unset($data['errorResponses']);
		}
		if (count($this->parameters) === 0) {
			unset($data['parameters']);
		}
		return $data;
	}
}

