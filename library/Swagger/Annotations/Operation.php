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

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Operation extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $dataType;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $paramType;

    /**
     * @var string
     */
    public $nickname;

    /**
     * @var string
     */
    public $responseClass;

    /**
     * @var string
     */
    public $summary;

    /**
     * @var string
     */
    public $httpMethod;

    /**
     * @var Parameters
     */
    public $parameters;

    /**
     * @var ErrorResponses
     */
    public $errorResponses;

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
        if (isset($values['value'])) {
            foreach ($values['value'] as $value) {
                switch ($value) {
                    case ($value instanceof Parameters):
                        $this->parameters = $value->toArray();
                        break;
                    case ($value instanceof ErrorResponses):
                        $this->errorResponses = $value->toArray();
                        break;
                }
            }
        }
        $this->notes = $this->removePreamble($this->notes);
    }
}

