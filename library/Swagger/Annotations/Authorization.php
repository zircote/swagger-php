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
 * Provide information about the authorization schemes provided on this API. 
 * @link https://github.com/wordnik/swagger-spec/blob/master/versions/1.2.md#514-authorizations-object
 *
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Authorization extends AbstractAnnotation
{

    /**
     * The type of the authorization scheme.
     * @var string
     */
    public $type;
    
    /**
     * Denotes how the API key must be passed. Valid values are "header" or "query".
     * @var string
     */
    public $passAs;
  
    /**
     * The name of the header or query parameter to be used when passing the API key.
     * @var  string
     */
    public $keyname;
 
    /**
     * A list of supported OAuth2 scopes.
     * @var Scope[]
     */
    public $scopes;
  
    /**
     * Detailed information about the grant types supported by the OAuth2 authorization scheme.
     * @var object
     */
    public $grantTypes;

    protected static $mapAnnotations = array(
        '\Swagger\Annotations\Scope' => 'scopes[]'
    );

    public function validate() {
        if (in_array($this->type, array('basicAuth', 'apiKey', 'oauth2')) === false) {
            Logger::warning('Unexpected '.$this->identity().'->type "'.$this->type.'", expection "basicAuth", "apiKey" or "oauth2" in '.$this->_context);
            return false; 
        }
        if ($this->type === 'apiKey' && (empty($this->passAs) || empty($this->keyname))) {
            Logger::notice('Fields "passAs" and "keyname" are required for '.$this->identity().'->type "apiKey"  in '.$this->_context);
        }
        return true;
    }   

}
