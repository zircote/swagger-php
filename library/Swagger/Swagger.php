<?php

namespace Swagger;

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
 * @category   Swagger
 * @package    Swagger
 */
use Swagger\Annotations\Resource;
use Swagger\Annotations\Model;

/**
 * @category   Swagger
 * @package    Swagger
 */
class Swagger
{
    /**
     * @var array|Resource
     */
    public $registry = array();

    /**
     * @var array|Model
     */
    public $models = array();

    /**
     * @var array
     */
    public $partials = array();

    /**
     *
     * @param string|array $paths Project root folder
     * @param string|array $excludePath Exclude paths
     * )
     */
    public function __construct($paths = array(), $excludePath = array())
    {
        if (is_string($paths)) {
            $paths = array($paths);
        }
        foreach ($paths as $path) {
            $this->scan($path, $excludePath);
        }
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
        Logger::warning('Swagger::discover($path) is deprecated, use new Swagger($paths)');
        if ($excludePath === null) {
            $excludePaths = array();
        } else {
            $excludePaths = explode(':', $excludePath);
        }
        return new self($path, $excludePaths);
    }

    /**
     *
     * @return mixed
     */
    public function getResourceList($options = array())
    {
        self::parseOptions($options, array(
            'prefix' => '/',
            'suffix' => '',
            'basePath' => null,
            'apiVersion' => null,
            'swaggerVersion' => '1.2',
            'output' => 'array',
            'json_pretty_print' => true, // for outputtype 'json'
        ));
        $result = array(
            'basePath' => $options['basePath'],
            'apiVersion' => $options['apiVersion'],
            'swaggerVersion' => $options['swaggerVersion'],
            'apis' => array()
        );
        foreach ($this->registry as $resource) {
            if ($resource->swaggerVersion > $result['swaggerVersion']) {
                $result['swaggerVersion'] = $resource->swaggerVersion;
            }
            if ($options['apiVersion'] === null && $resource->apiVersion > $result['apiVersion']) {
                $result['apiVersion'] = $resource->apiVersion;
            }
            $path = $options['prefix'].str_replace('/', '-', ltrim($resource->resourcePath, '/')).$options['suffix'];
            $result['apis'][] = array(
                'path' => $path,
                'description' => $resource->getDescription()
            );
        }
        if ($result['basePath'] === null) {
            unset($result['basePath']);
        }
        switch ($options['output']) {
            case 'array':
                return $result;
            case 'json':
                return self::jsonEncode($result, $options['json_pretty_print']);
            default:
                throw new \Exception('Invalid output type "'.$options['ouput'].'"');
        }
    }

    /**
     * @param $resourceName
     * @return mixed
     */
    public function getResource($resourceName, $options = array())
    {
        self::parseOptions($options, array(
            'output' => 'array',
            'json_pretty_print' => true, // for outputtype 'json'
            'defaultBasePath' => null,
            'defaultApiVersion' => null,
            'defaultSwaggerVersion' => '1.2',
        ));

        if (array_key_exists($resourceName, $this->registry) === false) {
            Logger::warning('Resource "'.$resourceName.'" not found, try "'.implode('", "', $this->getResourceNames()).'"');
            return false;
        }
        $resource = $this->registry[$resourceName];
        // Apply defaults
        if ($resource->basePath === null) {
            $resource->basePath = $options['defaultBasePath'];
        }
        if ($resource->apiVersion === null) {
            $resource->apiVersion = $options['defaultApiVersion'];
        }
        if ($resource->swaggerVersion === null) {
            $resource->swaggerVersion = $options['defaultSwaggerVersion'];
        }
        // Sort operation paths alphabetically with shortest first
        $apis = $resource->apis;

        $paths = array();
        foreach ($apis as $key => $api) {
            $paths[$key] = $api->path;
        }
        array_multisort($paths, SORT_ASC, $apis);
        $resource->apis = $apis;

        switch ($options['output']) {
            case 'array':
                return self::export($resource);
            case 'json':
                return $this->jsonEncode($resource, $options['json_pretty_print']);
            case 'object':
                return $resource;
        }
    }

    /**
     * Add resources to the registry and collect models.
     * @return Swagger
     */
    public function scan($path, $excludePath = null)
    {
        foreach ($this->getFiles($path, $excludePath) as $filename) {
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
            foreach ($parser->getPartials() as $id => $partial) {
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
                    $model = $this->resolveModel($operation->type);
                    if ($model) {
                        $models[] = $model;
                    }
                    foreach ($operation->parameters as $parameter) {
                        $model = $this->resolveModel($parameter->type);
                        if ($model) {
                            $models[] = $model;
                        }
                    }
                    foreach ($operation->responseMessages as $responseMessage) {
                        $model = $this->resolveModel($responseMessage->responseModel);
                        if ($model) {
                            $models[] = $model;
                        }
                    }
                }
                $models = $this->resolveModels($models);
                foreach (array_unique($models) as $model) {
                    $resource->models[$model] = $this->models[$model];
                }
            }
        }

        ksort($this->registry, SORT_ASC);
        return $this;
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
        $models = $input;
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
                if ($model && !in_array($model, $models)) {
                    array_push($models, $model);
                    $models = array_merge($models, $this->resolveModels($models));
                }
            }
        }
        return $models;
    }

    /**
     * @param string $path
     * @param string $excludePaths
     *
     * @return array
     */
    protected function getFiles($path, $excludePaths = array())
    {
        if (is_file($path)) {
            return array($path);
        }
        if (is_string($excludePaths)) {
            $excludePaths = array($excludePaths);
        }
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
     * Checks if the type is a Swagger primitive (case-insensitive)
     * @param string $type
     * @return bool
     */
    public static function isPrimitive($type)
    {
        $primitiveTypes = array('boolean', 'integer', 'number', 'string');
        $primitiveTypes = array_merge($primitiveTypes, array('byte', 'long', 'float', 'double', 'date', 'int', 'bool')); // Invalid notation, but meant to be primitive type
        return in_array(strtolower($type), $primitiveTypes);
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
            // primitive types
            'integer' => 'integer', // formats 'int32' or 'int64' for long,
            'number' => 'number', // formats 'float' or 'double'
            'string' => 'string', // formats '', 'byte', 'date' or 'date-time'
            'boolean' => 'boolean',
            // complex types
            'object' => 'object',
            'array' => 'array',
            // common mistakes
            'int' => 'integer',
            'long' => 'integer',
            'bool' => 'boolean',
            'date' => 'string',
            'datetime' => 'string',
            'byte' => 'string',
        );
        $mapFormats = array(
            'long' => 'int64',
            'byte' => 'byte',
            'date' => 'date',
            'datetime' => 'date-format',
        );
        if (array_key_exists(strtolower($type), $map) && array_search($type, $map) === false) { // Invalid notation for a primitive?
            if (array_key_exists(strtolower($type), $mapFormats)) { //
                Logger::notice('Invalid `type="'.$type.'"` use `type="'.$map[strtolower($type)].'",format="'.$mapFormats[strtolower($type)].'"` in '.Annotations\AbstractAnnotation::$context);
            } else {
                // Don't automaticly correct the type, this creates the incentive to use consistent naming in the doc comments.
                Logger::notice('Invalid `type="'.$type.'"` use `type="'.$map[strtolower($type)].'"` in '.Annotations\AbstractAnnotation::$context);
            }
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
     * @param Resource $resource
     * @param bool $prettyPrint
     *
     * @return mixed|null|string
     */
    public static function jsonEncode($resource, $prettyPrint = false)
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
     * @return array
     */
    public function getRegistry()
    {
        return $this->registry;
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

    /**
     * Validate options and apply default values.
     *
     * @param array $options Given options.
     * @param array $defaults Available options and their default values.
     * @return void
     */
    static function parseOptions(&$options, $defaults)
    {
        if (is_array($options) === false) {
            $backtrace = debug_backtrace();
            throw new \InvalidArgumentException('Expecting an options-array for '.$backtrace[1]['function'].'()');
        }
        foreach (array_keys($options) as $option) {
            if (array_key_exists($option, $defaults) === false) {
                $backtrace = debug_backtrace();
                throw new \InvalidArgumentException('Invalid option "'.$option.'" for '.$backtrace[1]['function'].'(), expecting "'.implode('", "', array_keys($defaults)).'"');
            }
        }
        $options = array_merge($defaults, $options);
    }
}
