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
     * @var array
     */
    protected $classList = array();
    /**
     * @var array
     */
    public $resourceList = array();
    /**
     * @var array
     */
    public $registry = array();
    /**
     * @var array
     */
    public $models = array();

	/**
	 * @var Parser
	 */
	protected $parser;
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
            $this->setCache(new ArrayCache());
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
				$this->parser = new Parser();
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
        $this->cacheKey = null;
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
     *
     * @return Swagger
     */
    protected function discoverClassAnnotations()
    {
		$this->registry = array();
		// Add resoures to the registry and collect models
		foreach ($this->classList as $class) {
			$result = $this->parser->parseClass($class);
			if ($result) {
				/* @var Resource $resource */
				foreach ($result['resources'] as $resource) {
					$this->registry[$resource->resourcePath] = $resource;
				}
				/* @var Model $model */
				foreach ($result['models'] as $model) {
					$this->models[$model->id] = $model;
				}
			}
		}

		/* @var Resource $resource */
        foreach ($this->registry as $i => $resource) {
            $models = array();
			/* @var Api $api */
            foreach ($resource->apis as $api) {
				/* @var Annotations\Operation $operation */
                foreach ($api->operations as $operation) {
                    if (isset($operation->responseClass) &&
                        array_key_exists($operation->responseClass, $this->models)) {
                        array_push($models, $operation->responseClass);
                    } elseif (
                        isset($operation->responseClass) && ($model = $this->modelType($operation->responseClass))
                        && in_array($model, $this->models)
                    ) {
                        array_push($models, $model);
                    }
					/* @var Annotations\Parameter $parameter */
					foreach ($operation->parameters as $parameter) {
						if (array_key_exists($parameter->dataType, $this->models)) {
							array_push($models, $parameter->dataType);
						} elseif (
							($model = $this->modelType($parameter->dataType)) && in_array($model, $this->models)
						) {
							array_push($models, $model);
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
            $this->getCache()->save($this->cacheKey, $this->serialize());
        }
        return $this;
    }


    /**
	 * Append all models to that used inside the $input models
     * @param array $input models
     * @return array
     */
    protected function resolveModels($input)
    {
        $models = array();
        foreach ($input as $v) {
            $type = false;
            foreach ($this->models[$v]->properties as $property) {
                if ($property->items !== null && self::isPrimitive($property->items->type) === false) {
                    if (isset($property->items->type)) {
                        $type = $property->items->type;
                    }
                } else {
                    $type = $property->type;
                }
                if ($type && (array_key_exists($type, $this->models) || $type = $this->modelType($type))) {
                    if (array_key_exists($type, $this->models) && !in_array($type, $models)) {
                        array_push($models, $type);
                        $models = array_merge($models, $this->resolveModels($models));
                    }
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
                $extension = pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION);
                if (in_array($extension, array('php', 'phtml'))) {
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
		$this->classList = array();
		// Collect classnames
        foreach ($this->getFileList() as $filename) {
            $classes = $this->getClasses($filename);
            foreach ($classes as $class) {
                $this->classList[] = $class;
            }
			if ($classes) {
				include_once($filename);
			}
        }
        $this->discoverClassAnnotations();
        return $this;
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
                        'apiVersion' => $resource->apiVersion,
                        'swaggerVersion' => $resource->swaggerVersion,
                        'basePath' => $resource->basePath,
                        'apis' => array()
                    );
                }
                $result['apis'][] = array(
					'path' => '/resources/' . str_replace(
						'/',
						'-',
						ltrim($resource->resourcePath, '/')
					) . '.{format}',
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
	 * @var bool
	 */
	static function isPrimitive($type)
	{
		$primitiveTypes = array('byte', 'boolean', 'int', 'long', 'float', 'double', 'string', 'date');
		$primitiveTypes[] = 'integer'; // Should be int
		$primitiveTypes[] = 'bool';
		return in_array(strtolower($type), $primitiveTypes);
	}

	/**
	 * @param string $type
	 * @var bool
	 */
	static function isContainer($type)
	{
		return in_array(strtolower($type), array('array', 'set', 'list'));
	}


	/**
	 * Build the array to be used in the json.
	 * @param mixed $data
	 */
	static function export($data) {
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
		foreach($data as $key => $value) {
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
    public function getResource($resourceName, $prettyPrint = true, $serialize = true)
    {
        if (array_key_exists($resourceName, $this->registry)) {
            // Sort operation paths alphabetically with shortest first
            $apis = $this->registry[$resourceName]->apis;

            $paths = array();
            foreach ($apis as $key => $api)
            {
                $paths[$key] = str_replace('.{format}', '', $api->path);
            }
            array_multisort($paths, SORT_ASC, $apis);

            $this->registry[$resourceName]->apis = $apis;
            return $this->jsonEncode($this->registry[$resourceName], $prettyPrint);
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

    /**
     * Enable to define several resources for the same registry
     * @param array $result Registry Annotation Discover
     */
    protected function addRegistryAnnotations($result)
    {
        if (array_key_exists($result['resourcePath'], $this->registry) ) {
            foreach ($result['apis'] as $operation) {
                $this->registry[$result['resourcePath']]['apis'][] = $operation;
            }
        } else {
            $this->registry[$result['resourcePath']] = $result;
        }
    }
}

