<?php
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
 * @category   Swagger
 * @package    Swagger
 */
namespace Swagger;
use DirectoryIterator,
ReflectionClass,
Swagger\Models,
Swagger\Model;

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
     * @var \Swagger\Resource
     */
    public $resources;
    /**
     *
     * @var Models
     */
    public $models;

    /**
     *
     * @param string $path
     * @param string $excludePath
     */
    public function __construct($path = null, $excludePath = null)
    {
        if ($path) {
            $this->_path = $path;
            $this->_excludePath = $excludePath;
            $this->_discoverServices();            
        }
    }

    /**
     *
     * @param string $path
     * @param string $excludePath
     * @return Swagger
     */
    public static function discover($path, $excludePath = null)
    {
        $swagger = new self($path, $excludePath);
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
    public function getResource($basePath, $prettyPrint = false)
    {
        $resources = $this->resources->getResource($basePath);
        $result    = array();
        foreach ($resources as $key => $resource) {
            $result['apis'][] = array(
                'path'        => $resource['path'],
                'description' => $resource['value']
            );
            $result           = array_merge(
                $result,
                array(
                     'basePath'       => $resource['basePath'],
                     'swaggerVersion' => $resource['swaggerVersion'],
                     'apiVersion'     => $resource['apiVersion']
                )
            );
        }
        return $this->jsonEncode($result, $prettyPrint);
    }

    /**
     * @param $basePath
     * @param $api
     * @return mixed|string
     */
    public function getApi($basePath, $api, $prettyPrint = false)
    {
        $resources   = $this->resources->getResource($basePath);
        $apiResource = $resources[$api];
        $apis        = array();
        $models      = array();
        foreach ($apiResource['apis'] as $api) {
            foreach ($api['operations'] as $op) {
                $responseClass = $op['responseClass'];
                if (preg_match('/List\[(\w+)\]/i', $responseClass, $match)) {
                    $responseClass = $match[1];
                }
                $models[$responseClass] =
                    $this->models->results[$responseClass];
            }
            $apis[] = $api;
        }
        $apiResource['apis']   = $apis;
        $apiResource['models'] = $models;
        return $this->jsonEncode($apiResource, $prettyPrint);
    }

    /**
     * @param $resource
     * @return array
     */
    public function getApis($resource)
    {
        $resources = $this->resources->getResource($resource);
        return $resources;
    }

    /**
     *
     * @return array
     */
    public function getClassList()
    {
        if (!$this->_classList) {
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
        if (!$this->_fileList) {
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
        if (!$path) {
            $path = $this->_path;
        }
        $excludePaths = isset($this->_excludePath) ? explode(':', $this->_excludePath) : array();
        $files = array();
        $dir   = new DirectoryIterator($path);
        /* @var $fileInfo DirectoryIterator */
        foreach ($dir as $fileInfo) {
            if (!$fileInfo->isDot()) {
                $skip = false;
                foreach ($excludePaths as $excludePath) {
                    if (strpos(realpath($fileInfo->getPathname()), $excludePath) === 0) {
                        $skip = true;
                        break;
                    }
                }
                if (true === $skip) {
                    continue;
                }
            }
            
            if (!$fileInfo->isDot() && !$fileInfo->isDir()) {
                if (preg_match('/\.php$/i', $fileInfo->getFilename())) {
                    array_push(
                        $files,
                        $path . DIRECTORY_SEPARATOR . $fileInfo->getFileName()
                    );
                }
            } elseif (!$fileInfo->isDot() && $fileInfo->isDir()) {
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
    protected function _getClasses($filename)
    {
        $classes = array();
        if (file_exists($filename)) {
            $toks = token_get_all(file_get_contents($filename));
            $count  = count($toks);
            $ns     = $this->_getNamespace($filename);
            for ($i = 2; $i < $count; $i++) {
                if ($toks[$i - 2][0] == T_CLASS &&
                    $toks[$i - 1][0] == T_WHITESPACE &&
                    $toks[$i][0] == T_STRING
                ) {
                    $classes[] = $ns . $toks[$i][1];
                }
            }
        }
        return $classes;
    }

    /**
     *
     * @param string $filename
     */
    protected function _getNamespace($filename)
    {
        $ns = '\\';
        
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            
            if (strpos($content, 'namespace') !== false) {
                $toks = token_get_all($content);
                $startIndex = null;
                $lineNumber = null;
                
                foreach ($toks as $index => $tok) {
                    if (isset($tok[0]) && T_NAMESPACE == $tok[0]) {
                        $startIndex = $index + 1;
                        $lineNumber = $tok[2];
                        continue;
                    }
                    
                    if (null !== $startIndex && $index > $startIndex) {
                        if (T_STRING === $tok[0] || T_NS_SEPARATOR === $tok[0]) {
                            if (T_NS_SEPARATOR !== $tok[0]) {
                                $ns .= $tok[1] . '\\';
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        
        return $ns;
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
                array_push($this->_classList, new ReflectionClass($class));
            }
        }
        $this
            ->setResources(new Resource($this->_classList))
            ->setModels(new Models($this->_classList));
        return $this;
    }

    /**
     *
     * @param Resource $resources
     * @return Swagger
     */
    public function setResources(Resource $resources)
    {
        $this->resources = $resources;
        return $this;
    }

    /**
     *
     * @param Models $models
     * @return Swagger
     */
    public function setModels(Models $models)
    {
        $this->models = $models;
        return $this;
    }

    public function jsonEncode($data, $prettyPrint = false)
    {
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $json = json_encode(
                $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );
        } else {
            $json = str_replace('\/', '/', json_encode($data));
        }
        if (!$prettyPrint) {
            return $json;
        }
        /* @see Zend_Json::prettyPrint */
        $toks = preg_split('|([\{\}\]\[,])|', $json, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = null;
        $idt = 0;
        $lb = "\n";
        $ind = '    ';
        $il = false;
        foreach($toks as $tok) {
            if($tok == '') {
                continue;
            }
            $pr = str_repeat($ind, $idt);
            if (!$il && ($tok == '{' || $tok == '[')) {
                $idt++;
                if (($result != '') && ($result[(strlen($result)-1)] == $lb)) {
                    $result .= $pr;
                }
                $result .= $tok . $lb;
            } elseif (!$il && ($tok == '}' || $tok == ']')) {
                $idt--;
                $pr = str_repeat($ind, $idt);
                $result .= $lb . $pr . $tok;
            } elseif (!$il && $tok == ',') {
                $result .= $tok . $lb;
            } else {
                $result .= ( $il ? '' : $pr ) . $tok;
                if ((substr_count($tok, '"')-substr_count($tok, '\"')) % 2 != 0) {
                    $il = !$il;
                }
            }
        }
        return $result;
    }
}
