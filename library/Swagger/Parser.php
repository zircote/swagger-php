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
use Doctrine\Common\Annotations\AnnotationException;
/**
 * @category   Swagger
 * @package    Swagger
 */
class Parser
{

	/**
	 * Allows Annotation classes to know which property or method in which class is being processed.
	 * @var string
	 */
	public static $current;

	/**
	 * @var string
	 */
	private static $currentClass;
	/**
	 * @var AnnotationReader
	 */
	private $reader;

	function __construct()
	{
		AnnotationRegistry::registerAutoloadNamespace(__NAMESPACE__, dirname(__DIR__));
		$this->reader = new AnnotationReader();
	}

	/**
	 *
	 * @param string $class FQCN
	 * @return array
	 */
	function parseClass($class)
	{
		self::$currentClass = $class;
		self::$current = $class;
		$resources = array();
		$models = array();
		try {
			$reflectionClass = new \ReflectionClass($class);
			$annotations = $this->reader->getClassAnnotations($reflectionClass);
			foreach ($annotations as $annotation) {
				if ($annotation instanceof Annotations\Resource) {
					$resource = $this->parseResource($annotation, $reflectionClass);
					if ($resource->validate()) {
						$resources[] = $resource;
					}
				}
				if ($annotation instanceof Annotations\Model) {
					$model = $this->parseModel($annotation, $reflectionClass);
					if ($model->validate()) {
						$models[] = $model;
					}
				}
			}
		} catch (AnnotationException $e) {
			Logger::warning($e);
			self::$current = null;
			self::$currentClass = null;
			return false;
		}
		self::$currentClass = null;
		self::$current = null;
		if (count($resources) === 0 && count($models) === 0) {
			return false;
		}
		return array(
			'resources' => $resources,
			'models' => $models
		);
	}

	/**
	 *
	 * @param Annotations\Resource $resource
	 * @param \ReflectionClass $rClass
	 * @return Annotations\Resource
	 */
	protected function parseResource($resource, $rClass)
	{
		if ($resource->resourcePath === null) { // No resourcePath give?
			// Assume Classname (without Controller suffix) matches the base route.
			$resource->resourcePath = '/'.lcfirst($this->stripNamespace($rClass->name));
			$resource->resourcePath = preg_replace('/Controller$/i', '', $resource->resourcePath);
		}
		foreach ($rClass->getMethods() as $rMethod) {
			try {
				self::$current = self::$currentClass.'->'.$rMethod->name.'()';
				$annotations = $this->reader->getMethodAnnotations($rMethod);
				foreach ($annotations as $annotation) {
					$resource->apis[] = $this->parseApi($annotation, $rMethod, $resource->resourcePath);
				}
			} catch (AnnotationException $e) {
				Logger::warning($e);
			}
		}
		return $resource;
	}

	/**
	 *
	 * @param Annotations\Api $api
	 * @param \ReflectionMethod $rMethod
	 * @param string $resourcePath
	 */
	protected function parseApi($api, $rMethod, $resourcePath)
	{
		if ($api->path === null) { // No path given?
			// Assume method (without Action suffix) on top the resourcePath
			$api->path = $resourcePath.'/'.preg_replace('/Action$/i', '', $rMethod->name);
		}
		foreach ($api->operations as $operation) {
			if ($operation->nickname === null) {
				$operation->nickname = $rMethod->name;
			}
		}
		return $api;
	}

	/**
	 *
	 * @param Annotations\Model $model
	 * @param \ReflectionClass $rClass
	 */
	protected function parseModel($model, $rClass) {
		if ($model->id === null) {
			$model->id = $this->stripNamespace($rClass->name);
		}
		foreach ($rClass->getProperties() as $rProperty) {
			try {
				self::$current = self::$currentClass.'->'.$rProperty->name;
				$annotations = $this->reader->getPropertyAnnotations($rProperty);
				foreach ($annotations as $annotation) {
					if ($annotation instanceof Annotations\Property) {
						$model->properties[] = $this->parsePropery($annotation, $rProperty);
					}
				}
			} catch (AnnotationException $e) {
				Logger::warning($e);
			}
		}
		return $model;
	}

	/**
	 *
	 * @param Annotations\Property $property
	 * @param \ReflectionProperty $rProperty
	 */
	protected function parsePropery($property, $rProperty)
	{
		if ($property->name === null) {
			$property->name = $rProperty->name;
		}
		if ($property->type === null) {
			if (preg_match('/@var\s+(\w+)/i', $rProperty->getDocComment(), $matches)) {
	            $type = (string)array_pop($matches);
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
					'datetime' => 'Date',
					'\\datetime' => 'Date',
					'list' => 'List',
					'set' => 'Set',
				);
				if (array_key_exists(strtolower($type), $map)) {
					$type = $map[strtolower($type)];
				}
				$property->type = $type;
			}
		}
		// @todo Extract description
		return $property;
	}

	/**
	 * Remove the namespace from the fully qualified classname
	 * @param  string $class
	 * @return string
	 */
	private function stripNamespace($class)
	{
		return basename(str_replace('\\','/', $class));
	}
}

?>