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
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Swagger\Annotations\Model;
use Swagger\Annotations\Resource;
use Swagger\Annotations\Property;

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
     * @var null|string
     */
    protected $defaultBasePath;

    /**
     * @var null|string
     */
    protected $defaultApiVersion;

    /**
     * @var null|string
     */
    protected $defaultSwaggerVersion;

    /**
     * @var array
     */
    public $resourceList = array();

    /**
     * @var array|Resource
     */
    public $registry = array();

    /**
     * @var array
     */
    public $models = array();

    /**
     * @var array
     */
    public $partials = array();

    /**
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKey;
    
    /**
     * @var int
     */
    protected $cacheLifeTime = 3600;

    /**
     * @param null $path
     * @param null $excludePath
     * @param \Doctrine\Common\Cache\CacheProvider $cache
     */
    public function __construct($path = null, $excludePath = null, CacheProvider $cache = null)
    {
        if (null == $cache) {
            $this->setCache(new ArrayCache());
        } else {
            $this->setCache($cache);
        }
        if ($path) {
            $this->path = $path;
            $this->excludePath = $excludePath;
            $this->cacheKey = sha1($this->path.$this->excludePath);
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
        $this->excludePath = null;
        $this->classList = array();
        $this->fileList = array();
        $this->registry = array();
        $this->models = array();
        $this->partials = array();
        $this->cacheKey = null;
        return $this;
    }

    /**
     *
     * @return Swagger
     */
    protected function discoverServices()
    {
        $this->registry = array();
        // Add resoures to the registry and collect models
        foreach ($this->getFileList() as $filename) {
            $parser = new Parser($filename);
            foreach ($parser->getResources() as $resource) {
                if (array_key_exists($resource->resourcePath, $this->registry)) {
                    $this->registry[$resource->resourcePath]->merge($resource);
                } else {
                    $this->registry[$resource->resourcePath] = $resource;
                }
            }
            foreach ($parser->getModels() as $model) {
                $this->models[$model->id] = $model;
            }
            foreach ($parser->getPartials() as $id => $partial)
            {
                if (isset($this->partials[$id])) {
                    Logger::notice('partial="'.$id.'" is not unique.');
                }
                $this->partials[$id] = $partial;
            }
        }

        $this->applyPartials($this->registry);
        $this->applyPartials($this->models);
        foreach ($this->partials as $partial) {
            if ($partial->_partialId !== null) {
                Logger::notice('partial="'.$partial->_partialId.'" is was not used.');
            }
        }

        foreach ($this->models as $model) {
            $this->inheritProperties($model);
        }

        foreach ($this->registry as $resource) {

            $models = array();
            foreach ($resource->apis as $api) {
                foreach ($api->operations as $operation) {
                    $model = $this->resolveModel($operation->responseClass);
                    if ($model) {
                        $models[] = $model;
                    }
                    foreach ($operation->parameters as $parameter) {
                        $model = $this->resolveModel($parameter->dataType);
                        if ($model) {
                            $models[] = $model;
                        }
                    }
                }
                $models = array_merge($models, $this->resolveModels($models));
                foreach (array_unique($models) as $model) {
                    $resource->models[$model] = $this->models[$model];
                }
            }
        }

        ksort($this->registry, SORT_ASC);
        if ($this->getCache()) {
            $this->getCache()->save($this->cacheKey, $this->serialize(),$this->getCacheLifeTime());
        }
        return $this;
    }

    /**
     * @param Resource $resource
     */
    protected function applyDefaults($resource)
    {
        if ($resource->basePath === null) {
            $resource->basePath = $this->getDefaultBasePath();
        }
        if ($resource->swaggerVersion === null) {
            $resource->swaggerVersion = $this->getDefaultSwaggerVersion();
        }
        if ($resource->apiVersion === null) {
            $resource->apiVersion = $this->getDefaultApiVersion();
        }
    }

    /**
     * Resolve and apply all partials in the given nodetree.
     * @param Annotations\AbstractAnnotation|array $node
     */
    protected function applyPartials($node, $depth = 0)
    {
        static $active = array();

        if (is_array($node)) {
            foreach ($node as $annotation) {
                 $this->applyPartials($annotation, $depth + 1);
            }
        } else if ($node instanceof Annotations\AbstractAnnotation) {
            foreach ($node->_partials as $i => $id) {
                unset($node->_partials[$i]);
                if (empty($this->partials[$id])) {
                    Logger::notice('Partial "'.$id.'" not found.');
                    continue;
                }
                $partial = $this->partials[$id];
                $partial->_partialId = null; // Mark as used.
                if (isset($active[$id])) {
                    Logger::notice('Cyclic dependancy for partial "'.$id.'" detected.');
                    return;
                }
                $active[$id] = true;
                $this->applyPartials($partial, $depth + 1); // Resolve any partials inside the partial
                unset($active[$id]);
                if ($partial instanceof $node) { // Same type?
                    // Overwrite empty properties with the properties in the partial
                    foreach ($partial as $property => $value) {
                        if (!empty($value) && empty($node->$property)) {
                            $node->$property = $value;
                        }
                    }
                } else {
                    $node->setNestedAnnotations(array($partial));
                }
            }

            foreach ($node as $property => $value) {
                if (is_array($value) || is_object($value)) {
                    $this->applyPartials($value, $depth + 1);
                }
            }
            if ($node instanceof Annotations\Resource || $node instanceof Annotations\Model) {
                $node->validate();
            }
        }
    }

    /**
     *
     * @param string|null $model
     * @return string|false  false if $model doesn't contain a discoverd model.
     */
    protected function resolveModel($model)
    {
        if ($model === null) {
            return false;
        }
        if (preg_match('/(List|Array|Set)\[(\w+)\]|\$ref:(\w+)/', $model, $matches)) {
            $model = array_pop($matches);
        }
        if (array_key_exists($model, $this->models)) {
            return $model;
        }
        return false;
    }

    /**
     * Append all models to that referenced inside the $input models
     * @param array $input models  Example: array('Pet', 'Order')
     * @return array
     */
    protected function resolveModels($input)
    {
        $models = array();
        foreach ($input as $name) {
            $type = false;
            foreach ($this->models[$name]->properties as $property) {
                if ($property->items !== null && self::isPrimitive($property->items->type) === false) {
                    if (isset($property->items->type)) {
                        $type = $property->items->type;
                    }
                } else {
                    $type = $property->type;
                }
                $model = $this->resolveModel($type);
                if ($model !== $name && $model && !in_array($type, $models)) {
                    array_push($models, $model);
                    $models = array_merge($models, $this->resolveModels($models));
                }
            }
        }
        return $models;
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
        $swagger = new self($path, $excludePath, null);
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
                    if (strpos(realpath($fileInfo->getPathname()), realpath($excludePath)) === 0) {
                        $skip = true;
                        break;
                    }
                }
                if (realpath($fileInfo->getPathname()) === dirname(dirname(__DIR__)).'/tests') {
                    $skip = true;
                    Logger::notice('Skipping files in "'.realpath($fileInfo->getPathname()).'" add your "vendor" directory to the exclude paths');
                }
                if (realpath($fileInfo->getPathname()) === realpath(__DIR__.'/../../../../doctrine')) {
                    $skip = true;
                    Logger::notice('Skipping files in "'.realpath($fileInfo->getPathname()).'" add your "vendor" directory to the exclude paths');
                }
                if (true === $skip) {
                    continue;
                }
            }
            if (!$fileInfo->isDot() && !$fileInfo->isDir()) {
                $extension = pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION);
                if (in_array($extension, array('php', 'phtml'))) {
                    array_push($files, $path.DIRECTORY_SEPARATOR.$fileInfo->getFileName());
                }
            } elseif (!$fileInfo->isDot() && $fileInfo->isDir()) {
                $files = array_merge($files, $this->getFiles($path.DIRECTORY_SEPARATOR.$fileInfo->getFileName()));
            }
        }
        return $files;
    }

    /**
     * @param bool $prettyPrint
     * @param bool $serialize
     * @return mixed|null|string
     */
    public function getResourceList($prettyPrint = true, $serialize = true)
    {
        if ($this->registry) {
            $result = array();
            foreach ($this->registry as $resource) {
                if (!$result) {
                    $result = array(
                        'apiVersion' => $this->getDefaultApiVersion() ?: $resource->apiVersion,
                        'swaggerVersion' => $this->getDefaultSwaggerVersion() ?: $resource->swaggerVersion,
                        'apis' => array()
                    );
                }
                $path = '/resources/'.str_replace('/', '-', ltrim($resource->resourcePath, '/')).'.{format}';
                $result['apis'][] = array(
                    'path' => $path,
                    'description' => $resource->apis[0]->description
                );
            }
            $this->resourceList = $result;
        }

        if ($serialize) {
            return $this->jsonEncode($this->resourceList, $prettyPrint);
        }

        return $this->resourceList;
    }

    /**
     * Checks if the type is a Swagger primitive (case-insensitive)
     * @param string $type
     * @return bool
     */
    public static function isPrimitive($type)
    {
        $primitiveTypes = array('byte', 'boolean', 'int', 'long', 'float', 'double', 'string', 'date');
        $primitiveTypes[] = 'integer'; // Should be int
        $primitiveTypes[] = 'bool';
        return in_array(strtolower($type), $primitiveTypes);
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function isContainer($type)
    {
        return in_array(strtolower($type), array('array', 'set', 'list'));
    }

    /**
     * Log a notice when the type doesn't exactly match the Swagger spec.
     * @link https://github.com/wordnik/swagger-core/wiki/Datatypes
     *
     * @param string $type
     * @return void
     */
    public static function checkDataType($type)
    {
        $map = array(
            'array' => 'Array',
            'byte' => 'byte',
            'boolean' => 'boolean',
            'bool' => 'boolean',
            'int' => 'int',
            'integer' => 'int',
            'long' => 'long',
            'float' => 'float',
            'double' => 'double',
            'string' => 'string',
            'date' => 'Date',
            'list' => 'List',
            'set' => 'Set',
        );
        if (array_key_exists(strtolower($type), $map) && array_search($type, $map) === false) {
            // Don't correct the type, this creates the incentive to use consistent naming in the doc comments.
            Logger::notice('Encountered type "'.$type.'", did you mean "'.$map[strtolower($type)].'" in '.Annotations\AbstractAnnotation::$context);
        }
    }

    /**
     * Build the array to be used in the json.
     * @param mixed $data
     * @return mixed
     */
    public static function export($data)
    {
        if (is_object($data)) {
            if (method_exists($data, 'jsonSerialize')) {
                $data = $data->jsonSerialize();
            } else {
                $data = get_object_vars($data);
            }
        }
        if (is_array($data) === false) {
            return $data;
        }
        $output = array();
        foreach ($data as $key => $value) {
            $output[$key] = self::export($value);
        }
        return $output;
    }

    /**
     * @param      $data
     * @param bool $prettyPrint
     *
     * @return mixed|null|string
     */
    public function jsonEncode($resource, $prettyPrint = false)
    {
        $data = self::export($resource);
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $options = JSON_UNESCAPED_SLASHES;
            if ($prettyPrint) {
                $options |= JSON_PRETTY_PRINT;
            }

            return json_encode($data, $options);
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
                $result .= $token.$lineBreak;
            } elseif (!$indentLine && ($token == '}' || $token == ']')) {
                $indentTotal--;
                $preText = str_repeat($indent, $indentTotal);
                $result .= $lineBreak.$preText.$token;
            } elseif (!$indentLine && $token == ',') {
                $result .= $token.$lineBreak;
            } else {
                $result .= ($indentLine ? '' : $preText).$token;
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
    public function getResource($resourceName, $prettyPrint = true, $serialize = true)
    {
        if (array_key_exists($resourceName, $this->registry)) {
            $resource = $this->registry[$resourceName];
            $this->applyDefaults($resource);
            // Sort operation paths alphabetically with shortest first
            $apis = $resource->apis;

            $paths = array();
            foreach ($apis as $key => $api) {
                $paths[$key] = str_replace('.{format}', '', $api->path);
            }
            array_multisort($paths, SORT_ASC, $apis);

            $resource->apis = $apis;
            return $this->jsonEncode($resource, $prettyPrint);
        }
        Logger::warning('Resource "'.$resourceName.'" not found, try "'.implode('", "', $this->getResourceNames()).'"');
        return false;
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
     *
     * @param int $lifetime
     * @return Swagger
     */
    public function setCacheLifeTime($lifetime)
    {
        $this->cacheLifeTime = $lifetime;
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
     * @return null|string
     */
    public function getCacheLifeTime()
    {
        return $this->cacheLifeTime;
    }

    /**
     * @param string $url
     * @return Swagger fluent api
     */
    public function setDefaultBasePath($url)
    {
        $this->defaultBasePath = $url;
        return $this;
    }

    public function getDefaultBasePath()
    {
        return $this->defaultBasePath;
    }

    /**
     * @param string $version
     * @return Swagger fluent api
     */
    public function setDefaultApiVersion($version)
    {
        $this->defaultApiVersion = $version;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDefaultApiVersion()
    {
        return $this->defaultApiVersion;
    }

    /**
     * @param string $version
     * @return Swagger fluent api
     */
    public function setDefaultSwaggerVersion($version)
    {
        $this->defaultSwaggerVersion= $version;
        return $this;
    }
    /**
     * @return null|string
     */
    public function getDefaultSwaggerVersion()
    {
        return $this->defaultSwaggerVersion;
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
        $this->cache->save($this->cacheKey, $this->serialize(), $this->getCacheLifeTime);
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

    protected function inheritProperties($model)
    {
        if ($model->phpExtends === null) {
            return; // model doesn't have a superclass (or is already resolved)
        }
        $parent = false;
        foreach ($this->models as $super) {
            if ($model->phpExtends === $super->phpClass) {
                $parent = $super;
                break;
            }
        }
        if ($parent === false) {
            return; // Superclass not discoved or doesn't have annotations
        }
        $model->phpExtends = null;
        $this->inheritProperties($parent);
        foreach ($parent->properties as $parentProperty) {
            $exists = false;
            foreach ($model->properties as $property) {
                if ($property->name == $parentProperty->name) {
                    $exists = true;
                    break;
                }
            }
            if ($exists === false) {
                $model->properties[] = clone $parentProperty; // Inherit property
            }
        }
    }
}
