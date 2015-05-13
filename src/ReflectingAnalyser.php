<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\TokenParser;
use Swagger\Annotations\AbstractAnnotation;

/**
 * ReflectingAnalyser extracts swagger-php annotations using reflection.
 */
class ReflectingAnalyser implements AnalyserInterface
{
    /** @var AnnotationReader */
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * Create a new reader using the default annotation reader.
     *
     * @return static
     */
    public static function createDefault()
    {
        return new static(new AnnotationReader());
    }

    /**
     * Extract and process all annotations from a file.
     *
     * @param string $filename Path to a php file.
     *
     * @return AbstractAnnotation[]
     */
    public function fromFile($filename)
    {
        $class = $this->getClassName($filename);
        if ($class === null) {
            return [];
        }

        $annotations = $this->parseClass(new \ReflectionClass($class));

        return $annotations;
    }

    private function parseClass(\ReflectionClass $class)
    {
        $createMethod   = function (\ReflectionMethod $method) {
            return new ReflectedAnnotations($method, $this->annotationReader->getMethodAnnotations($method));
        };
        $createProperty = function (\ReflectionProperty $property) {
            return new ReflectedAnnotations($property, $this->annotationReader->getPropertyAnnotations($property));
        };

        $annotations = new ClassAnnotations(
            new ReflectedAnnotations($class, $this->annotationReader->getClassAnnotations($class)),
            array_map($createMethod, $class->getMethods()),
            array_map($createProperty, $class->getProperties())
        );

        return $annotations->getAnnotations();
    }

    private function getClassName($filename)
    {
        $parser = new TokenParser(file_get_contents($filename));

        while ($token = $parser->next()) {
            if ($token[0] === T_NAMESPACE) {
                $namespace = $parser->parseNamespace();
            } elseif ($token[0] === T_CLASS) {
                $class = $parser->parseClass();
                break;
            }
        }

        return isset($class) ? (isset($namespace) ? $namespace . '\\' . $class : $class) : null;
    }
}
