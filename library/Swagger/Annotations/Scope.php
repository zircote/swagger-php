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
use Swagger\Logger;

/**
 * Describes an OAuth2 authorization scope.
 * @link https://github.com/wordnik/swagger-spec/blob/master/versions/1.2.md#516-scope-object
 *
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Scope extends AbstractAnnotation
{

    /**
     * The name of the scope.
     * @var string
     */
    public $scope;
    
    /**
     * A short description of the scope.
     * @var string
     */
    public $description;

	public function validate()
    {
        if (empty($this->scope)) {
            Logger::warning('Required field "scope" is missing for "'.$this->identity().'" in '.$this->_context);
            return false;
        }
        return true;
	}

}
