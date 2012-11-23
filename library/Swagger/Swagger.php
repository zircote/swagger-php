<?php
namespace Swagger;

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
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;
use \Doctrine\Common\Cache\CacheProvider;
use Swagger\Annotations\Model;
use Swagger\Annotations\Resource;

/**
 * @category   Swagger
 * @package    Swagger
 */
class Swagger implements \Serializable
{

    /**
     *
     * @var Array
     */
    protected $fileList = array();
    /**
     * @var string
     */
    protected $path;
    /**
     * @var null|string
     */
    protected $excludePath;

    /**
     * @var array
     */
    protected $classList = array();

    /**
     * @var array
     */
    public $registry = array();
    /**
     * @var array
     */
    public $models = array();
    /**
     * @var Reader
     */
    protected $reader;
    /**
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $cache;
    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * @param null $path
     * @param null $excludePath
     * @param \Doctrine\Common\Cache\CacheProvider $cache
     */
    public function __construct($path = null, $excludePath = null, CacheProvider $cache = null)
    {
        if (null == $cache) {
            $this->setCache(new \Doctrine\Common\Cache\ArrayCache());
        } else {
            $this->setCache($cache);
        }
        if ($path) {
            $this->path = $path;
            $this->excludePath = $excludePath;
            $this->cacheKey = sha1($this->path . $this->excludePath);
            if ($this->cache->contains($this->cacheKey)) {
                $this->unserialize($this->cache->fetch($this->cacheKey));
            } else {
                $this->discoverServices();
            }
        }
    }

    /**
     * @return Swagger
     */
    public function reset()
    {
        $this->reader = null;
        $this->excludePath = null;
        $this->classList = array();
        $this->fileList = array();
        $this->registry = array();
        $this->models = array();
        $this->cacheKey = null;
        return $this;
    }
    /**
     * @return Swagger
     */
    protected function initializeAnnotations()
    {
        if (!$this->reader) {
            AnnotationRegistry::registerAutoloadNamespace(__NAMESPACE__, dirname(__DIR__));
            $this->reader = new IndexedReader(new AnnotationReader());
        }
        return $this;
    }

    /**
     * @param $test
     * @return bool|mixed
     */
    protected function modelType($test)
    {
        if (preg_match('/List\[(\w+)\]|\$ref:(\w+)/', $test, $matches)) {
            return array_pop($matches);
        }
        return false;
    }

    /**
     * @todo clean me please...
     *
     * @return Swagger
     */
    protected function discoverClassAnnotations()
    {
        $this->registry = array();
        $registry = array();
        $result = array();
        $this->initializeAnnotations();
        /* @var \ReflectionClass $class */
        foreach ($this->classList as $class) {
            $result = $this->getClassAnnotations($class);
            if ($result) {
                $this->registry[$result['resourcePath']] = $result;
            }
        }
        foreach ($this->registry as $res) {
            foreach ($res['apis'] as $apis) {
                $apis = array_pop($apis);
                $op = array_pop($apis['operations'])->toArray();
                $result[$apis['path']][] = $op;
            }
        }
        foreach ($this->registry as $index => $resource) {
            foreach ($resource as $k => $v) {
                if ($k != 'apis') {
                    $registry[$index][$k] = $v;
                }
            }
            $models = array();
            foreach ($resource['apis'] as $api) {
                $api = array_pop($api);
                unset($api['operations']);
                $op = (array)@$result[$api['path']];
                $api['operations'] = $op;
                foreach ($op as $operation) {
                    if (array_key_exists($operation['responseClass'], $this->models)) {
                        array_push($models, $operation['responseClass']);
                    } elseif (
                        ($model = $this->modelType($operation['responseClass'])) && in_array($model, $this->models)
                    ) {
                        array_push($models, $model);
                    }
                    if (isset($operation['parameters'])) {
                        foreach ($operation['parameters'] as $parameter) {
                            if (array_key_exists($parameter['dataType'], $this->models)) {
                                array_push($models, $parameter['dataType']);
                            } elseif (
                                ($model = $this->modelType($parameter['dataType'])) && in_array($model, $this->models)
                            ) {
                                array_push($models, $model);
                            }
                        }
                    }
                }
                foreach ($models as $v) {
                    $type = false;
                    foreach ($this->models[$v]['properties'] as $property) {
                        if ($property['type'] == 'Array' && (isset($property['items']) && is_array($property['items']))
                        ) {
                            if (isset($property['items']['$ref'])) {
                                $type = $property['items']['$ref'];
                            }
                        } else {
                            $type = $property['type'];
                        }
                        if ($type && (array_key_exists($type, $this->models) || $type = $this->modelType($type))) {
                            if (array_key_exists($type, $this->models)) {
                                array_push($models, $type);
                            }
                        }
                    }
                }
                $registry[$index]['apis'][$api['path']][] = $api;
                foreach (array_unique($models) as $model) {
                    $registry[$index]['models'][$model] = $this->models[$model];
                }
            }
        }
        foreach ($registry as $index => $reg) {
            foreach ($reg['apis'] as $k => $api) {
                unset($registry[$index]['apis'][$k]);
                $registry[$index]['apis'][] = array_pop($api);
            }

        }
        $this->registry = $registry;
        if ($this->getCache()) {
            $this->getCache()->save($this->cacheKey, $this->serialize());
        }
        return $this;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return array
     */
    protected function getClassAnnotations(\ReflectionClass $class)
    {
        /* @var \Swagger\Annotations\Resource|Model $resource */
        $resource = null;
        foreach ($this->reader->getClassAnnotations($class) as $result) {
            if ($result instanceof Model) {
                /* @var Model $result */
                /* @var \ReflectionProperty $property */
                foreach ($class->getProperties() as $property) {
                    $result->properties = array_merge(
                        $result->properties,
                        $this->discoverPropertyAnnotations($property)
                    );
                }
                $this->models[$result->id] = $result->toArray();
            } elseif ($result instanceof Resource) {
                $resource = $result;
                /* @var \ReflectionMethod $method */
                foreach ($class->getMethods() as $method) {
                    $resource->apis[] = $this->discoverMethodAnnotations($method);
                }
                $resource = $resource->toArray();
            }
        }
        return $resource;
    }

    /**
     * @param \ReflectionMethod $method
     *
     * @return array
     */
    protected function discoverMethodAnnotations(\ReflectionMethod $method)
    {
        $result = array();
        /* @var \Swagger\Annotations\AbstractAnnotation $method */
        foreach ($this->reader->getMethodAnnotations($method) as $method) {
            array_push($result, $method->toArray());
        }
        return $result;
    }

    /**
     * @param \ReflectionProperty $property
     *
     * @return array
     */
    protected function discoverPropertyAnnotations(\ReflectionProperty $property)
    {
        $result = array();
        /* @var \Swagger\Annotations\AbstractAnnotation $property */
        foreach ($this->reader->getPropertyAnnotations($property) as $property) {
            array_push($result, $property->toArray());
        }
        return $result;
    }

    /**
     * @static
     *
     * @param      $path
     * @param null $excludePath
     *
     * @return Swagger
     */
    public static function discover($path, $excludePath = null)
    {
        $swagger = new self($path, $excludePath);
        return $swagger;
    }

    /**
     *
     * @return array
     */
    public function getFileList()
    {
        if (!$this->fileList) {
            $this->setFileList($this->getFiles());
        }
        return $this->fileList;
    }

    /**
     *
     * @param  array $fileList
     *
     * @return Swagger
     */
    public function setFileList($fileList)
    {
        $this->fileList = $fileList;
        return $this;
    }

    /**
     * @param null $path
     *
     * @return array
     */
    protected function getFiles($path = null)
    {
        if (!$path) {
            $path = $this->path;
        }
        $excludePaths = isset($this->excludePath) ? explode(':', $this->excludePath) : array();
        $files = array();
        $dir = new \DirectoryIterator($path);
        /* @var $fileInfo \DirectoryIterator */
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
                if (in_array($fileInfo->getExtension(), array('php', 'phtml'))) {
                    array_push($files, $path . DIRECTORY_SEPARATOR . $fileInfo->getFileName());
                }
            } elseif (!$fileInfo->isDot() && $fileInfo->isDir()) {
                $files = array_merge($files, $this->getFiles($path . DIRECTORY_SEPARATOR . $fileInfo->getFileName()));
            }
        }
        return $files;
    }

    /**
     * @param $filename
     *
     * @return array
     */
    protected function getClasses($filename)
    {
        $classes = array();
        if (file_exists($filename)) {
            $tokens = token_get_all(file_get_contents($filename));
            $count = count($tokens);
            $namespace = $this->getNamespace($filename);
            for ($i = 2; $i < $count; $i++) {
                if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING
                ) {
                    $classes[] = $namespace . $tokens[$i][1];
                }
            }
        }
        return $classes;
    }

    /**
     * @param $filename
     *
     * @return string
     */
    protected function getNamespace($filename)
    {
        $namespace = '\\';

        if (file_exists($filename)) {
            $content = file_get_contents($filename);

            if (strpos($content, 'namespace') !== false) {
                $tokens = token_get_all($content);
                $startIndex = null;
                $lineNumber = null;

                foreach ($tokens as $index => $token) {
                    if (isset($token[0]) && T_NAMESPACE == $token[0]) {
                        $startIndex = $index + 1;
                        continue;
                    }

                    if (null !== $startIndex && $index > $startIndex) {
                        if (T_STRING === $token[0] || T_NS_SEPARATOR === $token[0]) {
                            if (T_NS_SEPARATOR !== $token[0]) {
                                $namespace .= $token[1] . '\\';
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
        }

        return $namespace;
    }

    /**
     *
     * @return Swagger
     */
    protected function discoverServices()
    {
        foreach ($this->getFileList() as $filename) {
            if ($filename) {
                include_once $filename;
            }
            foreach ($this->getClasses($filename) as $class) {
                array_push($this->classList, new \ReflectionClass($class));
            }
        }
        $this->discoverClassAnnotations();
        return $this;
    }

    /**
     * @param      $data
     * @param bool $prettyPrint
     *
     * @return mixed|null|string
     */
    public function jsonEncode($data, $prettyPrint = false)
    {
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {
            $json = str_replace('\/', '/', json_encode($data));
        }
        if (!$prettyPrint) {
            return $json;
        }
        $tokens = preg_split('|([\{\}\]\[,])|', $json, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = null;
        $indentTotal = 0;
        $lineBreak = "\n";
        $indent = '    ';
        $indentLine = false;
        foreach ($tokens as $token) {
            if ($token == '') {
                continue;
            }
            $preText = str_repeat($indent, $indentTotal);
            if (!$indentLine && ($token == '{' || $token == '[')) {
                $indentTotal++;
                if (($result != '') && ($result[(strlen($result) - 1)] == $lineBreak)) {
                    $result .= $preText;
                }
                $result .= $token . $lineBreak;
            } elseif (!$indentLine && ($token == '}' || $token == ']')) {
                $indentTotal--;
                $preText = str_repeat($indent, $indentTotal);
                $result .= $lineBreak . $preText . $token;
            } elseif (!$indentLine && $token == ',') {
                $result .= $token . $lineBreak;
            } else {
                $result .= ($indentLine ? '' : $preText) . $token;
                if ((substr_count($token, '"') - substr_count($token, '\"')) % 2 != 0) {
                    $indentLine = !$indentLine;
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getResourceNames()
    {
        return array_keys($this->registry);
    }

    /**
     * @param $resourceName
     * @param bool $prettyPrint
     * @return bool|mixed|null|string
     */
    public function getResource($resourceName, $prettyPrint = true)
    {
        if (array_key_exists($resourceName, $this->registry)) {
            return $this->jsonEncode($this->registry[$resourceName], $prettyPrint);
        }
        return false;
    }

    /**
     *
     * @param \Doctrine\Common\Annotations\Reader $reader
     *
     * @return Swagger
     */
    public function setReader($reader)
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @return array
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @return null
     */
    public function getExcludePath()
    {
        return $this->excludePath;
    }

    /**
     * @return array
     */
    public function getClassList()
    {
        return $this->classList;
    }

    /**
     *
     * @param array $classlist
     *
     * @return Swagger
     */
    public function setClassList($classlist)
    {
        $this->classList = $classlist;
        return $this;
    }

    /**
     *
     * @param null $excludePath
     * @return Swagger
     */
    public function setExcludePath($excludePath)
    {
        $this->excludePath = $excludePath;
        return $this;
    }

    /**
     *
     * @param array $registry
     * @return Swagger
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
        return $this;
    }

    /**
     * @param $path
     *
     * @return Swagger
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return Swagger
     */
    public function flushCache()
    {
        $this->getCache()->delete($this->cacheKey);
        $this->discoverServices();
        return $this;
    }

    /**
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param \Doctrine\Common\Cache\CacheProvider $cache
     * @return Swagger
     */
    public function setCache(\Doctrine\Common\Cache\CacheProvider $cache)
    {
        $this->cache = $cache;
        $this->cache->save($this->cacheKey, $this->serialize());
        return $this;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(
            array(
                 'registry' => $this->registry,
                 'models' => $this->models,
                 'path' => $this->path,
                 'excludePath' => $this->excludePath
            )
        );

    }

    /**
     * @param string $serialized
     * @return Swagger
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->registry = $data['registry'];
        $this->models = $data['models'];
        $this->path = $data['path'];
        $this->excludePath = $data['excludePath'];
        return $this;
    }
}

