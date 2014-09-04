<?php
namespace Swagger\Annotations;

/**
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 *             Copyright [2014] [Robert Allen]
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
use Swagger\Annotations\Consumes;
use Swagger\Annotations\Items;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Parameters;
use Swagger\Annotations\Produces;
use Swagger\Annotations\ResponseMessage;
use Swagger\Annotations\ResponseMessages;
use Swagger\Logger;
use Swagger\Swagger;

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
     * This is the HTTP method required to invoke this operation--the allowable values are GET, HEAD, POST, PUT, PATCH, DELETE, OPTIONS.
     * @var string
     */
    public $method;

    /**
     * This is a short summary of what the operation does.
     * @var string (max 60 characters)
     */
    public $summary;

    /**
     * This is a required field provided by the server for the convenience of the UI and client code generator, and is used to provide a shebang in the swagger-ui.
     * @var string
     */
    public $nickname;

    /**
     * This is what is returned from the method--in short, it's either void, a simple-type, a complex or a container return value.
     * @var string
     */
    public $type;

    /**
     * @var Items
     */
    public $items;

    /**
     * These are the inputs to the operation.
     * @var Parameter[]
     */
    public $parameters = array();

    /**
     * An array describing the responseMessage cases returned by the operation.
     * @var ResponseMessage[]
     */
    public $responseMessages = array();

    /**
     * A longer text field to explain the behavior of the operation.
     * @var string
     */
    public $notes;

    /**
     * @var Produces[]
     */
    public $produces;

    /**
     * @var Consumes[]
     */
    public $consumes;

    /**
     * A list of authorizations required to execute this operation.
     * @var string|array
     */
    public $authorizations;

    /**
     * Declares this operation to be deprecated.
     * @var bool
     */
    public $deprecated;

    protected static $mapAnnotations = array(
        '\Swagger\Annotations\Parameter' => 'parameters[]',
        '\Swagger\Annotations\ResponseMessage' => 'responseMessages[]',
        '\Swagger\Annotations\Produces' => 'produces[]',
        '\Swagger\Annotations\Consumes' => 'consumes[]',
        '\Swagger\Annotations\Items' => 'items',
        '\Swagger\Annotations\Authorizations' => 'authorizations',
    );

    /**
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);
        $this->notes = $this->removePreamble($this->notes);
    }

    public function setNestedAnnotations($annotations)
    {
        foreach ($annotations as $index => $annotation) {
            if ($annotation instanceof Parameters) {
                foreach ($annotation->parameters as $parameter) {
                    $this->parameters[] = $parameter;
                }
                unset($annotations[$index]);
            } elseif ($annotation instanceof ResponseMessages) {
                foreach ($annotation->responseMessages as $responseMessage) {
                    $this->responseMessages[] = $responseMessage;
                }
                unset($annotations[$index]);
            }
        }
        return parent::setNestedAnnotations($annotations);
    }

    public function validate()
    {
        if (empty($this->nickname)) {
            Logger::notice('Required field "nickname" is missing for "'.$this->identity().'" in '.$this->_context);
        }
        Swagger::checkDataType($this->type, $this->_context);
        foreach ($this->parameters as $parameter) {
            if ($parameter->validate() == false) {
                return false;
            }
        }
        Items::validateContainer($this);
        Produces::validateContainer($this);
        Consumes::validateContainer($this);
        return true;
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        if (count($this->responseMessages) === 0) {
            unset($data['responseMessages']);
        }
        if (count($this->parameters) === 0) {
            unset($data['parameters']);
        }
        return $data;
    }
}
