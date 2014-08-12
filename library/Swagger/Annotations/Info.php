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
 * Describes the metadata about the API.
 * @link https://github.com/wordnik/swagger-spec/blob/master/versions/1.2.md#513-info-object
 *
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Info extends AbstractAnnotation
{

    /**
     * The title of the application.
     * @var string
     */
    public $title;

    /**
     * A short description of the application.
     * @var string
     */
    public $description;

    /**
     * A URL to the Terms of Service of the API.
     * @var string
     */
    public $termsOfServiceUrl;

    /**
     * An email to be used for API-related correspondence.
     * @var string
     */
    public $contact;

    /**
     * The license name used for the API.
     * @var string
     */
    public $license;

    /**
     * A URL to the license used for the API.
     * @var string
     */
    public $licenseUrl;

    public function validate()
    {
        foreach (array('title', 'description') as $required) {
            if (empty($this->$required)) {
                Logger::notice('Required field "'.$required.'" is missing for "'.$this->identity().'" in '.$this->_context);
                return false;
            }
        }
        return true;
    }
}
