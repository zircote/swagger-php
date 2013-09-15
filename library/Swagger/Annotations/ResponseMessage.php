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
/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 * @link https://github.com/wordnik/swagger-core/wiki/Response-Messages
 */
class ResponseMessage extends AbstractAnnotation
{
    /**
     * the HTTP response code of the error condition.
     * @var int
     */
    public $code;

    /**
     * The explanation of what will cause the error condition.
     * @var string
     */
    public $message;

    /**
     * An optional response class to accompany the response code.
     * @var string
     */
    public $responseModel;

    public function __construct(array $values = array())
    {
        parent::__construct($values);
        if ($this->code !== null) {
            $this->code = (int) $this->code;
        }
    }
}
