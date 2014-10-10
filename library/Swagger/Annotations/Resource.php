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
use Swagger\Logger;

/**
 * @package
 * @category
 * @subpackage
 *
 * @Annotation
 */
class Resource extends AbstractAnnotation
{
    /**
     * The basePath is the end-point of your API.
     * Not the Developer or the  Admin Portal but the endpoint that serves your API requests.
     * @var string
     */
    public $basePath;

    /**
     * the version of Swagger
     * @var string
     */
    public $swaggerVersion;

    /**
     * The version of your API
     * @var string
     */
    public $apiVersion;

    /**
     * @var string "/store"
     */
    public $resourcePath;

    /**
     * @var Api[]
     */
    public $apis = array();

    /**
     * @var array
     */
    public $models = array();

    /**
     * @var array
     */
    public $produces;

    /**
     * @var array
     */
    public $consumes;

    /**
     * The description in the resource listing (api-docs.json)
     * @var string
     */
    public $description;

    /**
     * @var Authorizations
     */
    public $authorizations;

    protected static $mapAnnotations = array(
        '\Swagger\Annotations\Api' => 'apis[]',
        '\Swagger\Annotations\Produces' => 'produces[]',
        '\Swagger\Annotations\Consumes' => 'consumes[]',
        '\Swagger\Annotations\Authorizations' => 'authorizations',
    );

    public function validate()
    {
        if (empty($this->resourcePath)) {
            Logger::warning('@SWG\Resource() is missing "resourcePath" in '.$this->_context);
            return false;
        }
        if ($this->swaggerVersion) {
            if (version_compare($this->swaggerVersion, '1.2', '<')) {
                Logger::warning('swaggerVersion: '.$this->swaggerVersion.' is no longer supported. Use 1.2 or higher');
                $this->swaggerVersion = null;
            }
        }
        $validApis = array();
        foreach ($this->apis as $api) {
            $append = true;
            foreach ($validApis as $validApi) {
                if ($api->path === $validApi->path) { // The same api path?
                    $append = false;

                    foreach ($api->_partials as $partial) {
                        if( !in_array($partial, $validApi->_partials) )
                        {
                            $validApi->_partials[] = $partial;
                        }
                    }

                    // merge operations
                    foreach ($api->operations as $operation) {
            			foreach( $validApi->operations as $validOperation ) {
                            if($operation->method === $validOperation->method) {
                                Logger::warning($api->identity().':'.$operation->method.' would overwrite existing similar operation definition, skipping it');
                            }
                            else {
                                $validApi->operations[] = $operation;
                            }
                        }
                    }
                    // merge description
                    if ($validApi->description === null) {
                        $validApi->description = $api->description;
                    } elseif ($api->description !== null && $api->description !== $validApi->description){
                        Logger::notice('Competing description for '.$validApi->identity().' in '.$validApi->_context.' and '.$api->_context);
                    }
                    break;
                }
            }
            if ($api->validate() && $append) {
                $validApis[] = $api;
            }
        }
        if (count($validApis) === 0 && count($this->_partials) === 0) {
            Logger::warning($this->identity().' doesn\'t have any valid api calls');
            return false;
        }
        $this->apis = $validApis;
        Produces::validateContainer($this);
        Consumes::validateContainer($this);
        return true;
    }

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        unset($data['description']);
        if (count($this->models) === 0) {
            unset($data['models']);
        }
        return $data;
    }

    /**
     * @param Resource $resource
     */
    public function merge($resource)
    {
        foreach ($resource->apis as $api) {
            $this->apis[] = $api;
        }
        $this->validate();
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        if ($this->description !== null) {
            return $this->description;
        }
        foreach ($this->apis as $api) {
            if ($api->description !== null) {
                return $api->description;
            }
        }
    }

    public function identity()
    {
        return '@SWG\Resource(resourcePath="'.$this->resourcePath.'")';
    }
}
