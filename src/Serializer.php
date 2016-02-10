<?php

namespace Swagger;

/**
 * Class AnnotationDeserializer is used to deserialize a json string
 * to a specific Swagger PHP Annotation class and vice versa.
 *
 * @link https://github.com/zircote/swagger-php
 *
 */
class Serializer
{
    const    Contact = 'Swagger\Annotations\Contact';
    const    Definition = 'Swagger\Annotations\Definition';
    const    Delete = 'Swagger\Annotations\Delete';
    const    ExternalDocumentation = 'Swagger\Annotations\ExternalDocumentation';
    const    Get = 'Swagger\Annotations\Get';
    const    Head = 'Swagger\Annotations\Head';
    const    Header = 'Swagger\Annotations\Header';
    const    Info = 'Swagger\Annotations\Info';
    const    Items = 'Swagger\Annotations\Items';
    const    License = 'Swagger\Annotations\License';
    const    Operation = 'Swagger\Annotations\Operation';
    const    Options = 'Swagger\Annotations\Options';
    const    Parameter = 'Swagger\Annotations\Parameter';
    const    Patch = 'Swagger\Annotations\Patch';
    const    Path = 'Swagger\Annotations\Path';
    const    Post = 'Swagger\Annotations\Post';
    const    Property = 'Swagger\Annotations\Property';
    const    Put = 'Swagger\Annotations\Put';
    const    Response = 'Swagger\Annotations\Response';
    const    Schema = 'Swagger\Annotations\Schema';
    const    SecurityScheme = 'Swagger\Annotations\SecurityScheme';
    const    Swagger = 'Swagger\Annotations\Swagger';
    const    Tag = 'Swagger\Annotations\Tag';
    const    Xml = 'Swagger\Annotations\Xml';

    private static $cachedNames;

    private static function getDefinedNames()
    {
        if (static::$cachedNames === null) {
            static::$cachedNames = [];
            $reflection = new \ReflectionClass(__CLASS__);
            static::$cachedNames = $reflection->getConstants();
        }

        return static::$cachedNames;
    }

    public static function isValidClassName($className)
    {
        return in_array($className, static::getDefinedNames());
    }

    /**
     * Serialize.
     *
     * @param Annotations\AbstractAnnotation $annotation
     * @return string
     */
    public function serialize(Annotations\AbstractAnnotation $annotation)
    {
        return json_encode($annotation);
    }

    /**
     * Deserialize
     *
     * @param $jsonString
     * @param $className
     *
     * @return Annotations\AbstractAnnotation
     *
     * @throws \Exception
     */
    public function deserialize($jsonString, $className)
    {
        if (!$this->isValidClassName($className)) {
            throw new \Exception($className.' is not defined in Swagger PHP Annotations');
        }

        return $this->doDeserialize(json_decode($jsonString), $className);
    }

    /**
     * Do deserialization.
     *
     * @param \stdClass $c
     * @param string    $class The class name of annotation.
     *
     * @return Annotations\AbstractAnnotation
     */
    private function doDeserialize(\stdClass $c, $class)
    {
        $annotation = new $class([]);
        foreach ($c as $property => $value) {
            if (substr($property, 0, 2) === 'x-') {
                $custom = substr($property, 2);
                $annotation->x[$custom] = $value;
            } else {
                $annotation->$property = $this->doDeserializeProperty($annotation, $property, $value);
            }
        }

        return $annotation;
    }

    /**
     * Deserialize the annotation's property.
     *
     * @param Annotations\AbstractAnnotation $annotation
     * @param string                         $property
     * @param mixed                          $value
     *
     * @return mixed
     */
    private function doDeserializeProperty(Annotations\AbstractAnnotation $annotation, $property, $value)
    {
        // property is primitive type
        if (array_key_exists($property, $annotation::$_types)) {
            return $value;
        }

        // property is embedded annotation
        foreach ($annotation::$_nested as $class => $declaration) {
            // property is an annotation
            if (is_string($declaration) && $declaration === $property) {
                return $this->doDeserialize($value, $class);
            }

            // property is an annotation array
            if (is_array($declaration) && count($declaration) === 1 && $declaration[0] === $property) {
                $annotationArr = [];
                foreach ($value as $v) {
                    $annotationArr[] = $this->doDeserialize($v, $class);
                }

                return $annotationArr;
            }

            // property is an annotation hash map
            if (is_array($declaration) && count($declaration) === 2 && $declaration[0] === $property) {
                $key = $declaration[1];
                $annotationHash = [];
                foreach ($value as $k => $v) {
                    $annotation = $this->doDeserialize($v, $class);
                    $annotation->$key = $k;
                    $annotationHash[$k] = $annotation;
                }

                return $annotationHash;
            }
        }

        return $value;
    }
}