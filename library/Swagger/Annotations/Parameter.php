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
use Swagger\Swagger;
use Swagger\Logger;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Parameter extends DataType
{
    /**
     * The type of the parameter. It can be only one of the following: "path", "query", "body", "header" or "form".
     * "path" is for when the parameter is part of the URL path (e.g /foo/{id}.xml)
     * "query" is for when the parameter is part of the query_string or a form
     * @var string
     */
    public $paramType;

    /**
     * The unique name for the parameter. Each name must be unique, even if they are associated with different paramType values.
     * @var string
     */
    public $name;

    /**
     * The type of the parameter.
     *
     * For path, query, and header paramTypes, this field must be a primitive.
     * For body, this can be a complex or container datatype.
     * When sending multiple values, the array type should be used
     * @var string
     */
    public $type;

    /**
     * For path, this is always true. Otherwise, this field tells the client whether or not the field must be supplied.
     * @var bool
     */
    public $required;

    public $defaultValue;

    public function __construct(array $values = array())
    {
        parent::__construct($values);
        if ($this->paramType && !in_array($this->paramType, array('path', 'query', 'body', 'header', 'form'))) {
            Logger::warning('Unexpected paramType "'.$this->paramType.'", expecting "path", "query", "body", "header" or "form" in '.AbstractAnnotation::$context);
        }
    }
}
