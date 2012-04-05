<?php
/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * Copyright [2012] [Robert Allen]
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
 * @category   Swagger
 * @package    Swagger
 */
require_once 'Swagger/Resource.php';
require_once 'Swagger/Models.php';
/**
 *
 *
 *
 * @category   Swagger
 * @package    Swagger
 */
class Swagger
{
    /**
     *
     * @var Array
     */
    protected $_classList = array();
    /**
     *
     * @var Array
     */
    protected $_fileList;
    /**
     *
     * @var Swagger_Resources
     */
    public $resources;
    /**
     *
     * @var Swagger_Models
     */
    public $models;
    /**
     *
     * @param string $path
     * @return array
     */
    public static function discover($path)
    {
        $swagger = new self($path);
        return $swagger;
    }
    /**
     * @return array
     */
    public function getResourceNames()
    {
        return $this->resources->getResources();
    }
    /**
     * @return array
     */
    public function getResource($basePath)
    {
        $resources = $this->resources->getResource($basePath);
        $result = array();
        foreach ($resources as $key => $resource) {
                $result['apis'][] = array(
                    'path' => $resource['basePath'] . $resource['path'],
                    'description' => $resource['value']
                );
            $result = array_merge($result,  array(
                'basePath' => $resource['basePath'],
                'swaggerVersion' => $resource['swaggerVersion'],
                'apiVersion' => $resource['apiVersion']
                )
            );
        }
        return json_encode($result);
    }
    public function getApi($basePath, $api)
    {
        $models = array();
        $resources = $this->resources->getResource($basePath);
        foreach ($resources[$api]['operations'] as $k => $op) {
            $path = @$resources[$api]['operations'][$k]['path'] ?: null;
            $resources[$api]['operations'][$k]['path'] = $basePath. $resources[$api]['path'] . $path;
            $responseClass = $op['responseClass'];
            if(preg_match('/List\[(\w+)\]/i', $responseClass, $match)){
                $responseClass = $match[1];
            }
            $models[$responseClass] = $responseClass;
        }
        $result = array_merge(array('models' => array()),$resources[$api]);
        foreach (array_keys($models) as $model) {
            array_push($result['models'], $this->models->results[$model]);
        }
        return json_encode($result);
    }
    /**
     *
     * @param string $path
     */
    public function __construct($path = null)
    {
        if($path){
            $this->_path = $path;
            $this->_discoverServices();
        }
    }
    /**
     *
     * @return array
     */
    public function getClassList()
    {
        if(!$this->_classList){
            $this->_discoverServices();
        }
        return $this->_classList;
    }
    /**
     *
     * @param array $classList
     * @return Swagger
     */
    public function setClassList(array $classList)
    {
        $this->_classList = $classList;
        return $this;
    }
    /**
     *
     * @return array
     */
    public function getFileList()
    {
        if(!$this->_fileList){
            $this->setFileList($this->_getFiles());
        }
        return $this->_fileList;
    }
    /**
     *
     * @param  array $fileList
     * @return Swagger
     */
    public function setFileList($fileList)
    {
        $this->_fileList = $fileList;
        return $this;
    }
    /**
     *
     * @param string|null $path
     * @return array[string]
     */
    protected function _getFiles($path = null)
    {
        if(!$path){
            $path = $this->_path;
        }
        $files = array();
        $dir = new DirectoryIterator($path);
        /* @var $fileInfo DirectoryIterator */
        foreach ($dir as $fileInfo) {
            if(!$fileInfo->isDot() && !$fileInfo->isDir()){
                if(preg_match('/\.php$/i',$fileInfo->getFilename())){
                    array_push($files, $path . DIRECTORY_SEPARATOR . $fileInfo->getFileName());
                }
            } elseif(!$fileInfo->isDot() && $fileInfo->isDir()){
                $files = array_merge(
                    $files,
                    $this->_getFiles(
                        $path . DIRECTORY_SEPARATOR . $fileInfo->getFileName()
                    )
                );
            }
        }
        return $files;
    }
    /**
     *
     * @param string $filename
     */
    protected function _getClasses($filename) {
        $classes = array();
        if(file_exists($filename)){
            $tokens = token_get_all(file_get_contents($filename));
            $count = count($tokens);
            for ($i = 2; $i < $count; $i++) {
                if ($tokens[$i - 2][0] == T_CLASS &&
                    $tokens[$i - 1][0] == T_WHITESPACE &&
                    $tokens[$i][0] == T_STRING) {
                    $classes[] = $tokens[$i][1];
                }
            }
        }
        return $classes;
    }
    /**
     *
     * @return Swagger
     */
    protected function _discoverServices()
    {
        foreach ($this->getFileList() as $filename) {
            require_once $filename;
            foreach ($this->_getClasses($filename) as $class) {
                array_push($this->_classList,new ReflectionClass($class));
            }
        }
        $this->setResources(new Swagger_Resource($this->_classList))
            ->setModels(new Swagger_Models($this->_classList));
        return $this;
    }
    /**
     *
     * @param Swagger_Resource $resources
     * @return Swagger
     */
    public function setResources(Swagger_Resource $resources)
    {
        $this->resources = $resources;
        return $this;
    }
    /**
     *
     * @param Swagger_Models $models
     * @return Swagger
     */
    public function setModels(Swagger_Models $models)
    {
        $this->models = $models;
        return $this;
    }
}