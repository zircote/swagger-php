<?php

namespace Swagger\Annotations;

use Doctrine\Common\Annotations\Reader;

class IndexedReader implements Reader
{
    /**
     * @var Reader
     */
    private $delegate;

    /**
     * Constructor
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->delegate = $reader;
    }

    /**
     * Get Annotations for class
     *
     * @param \ReflectionClass $class
     * @return array
     */
    public function getClassAnnotations(\ReflectionClass $class)
    {
        $annotations = array();
        foreach ($this->delegate->getClassAnnotations($class) as $annot) {
            // Enable to have several annotation of the same class
            if (empty($annotations[get_class($annot)] ) ) {
                $annotations[get_class($annot)] = array();
            }
            $annotations[get_class($annot)][] = $annot;
        }

        return $annotations;
    }

    /**
     * Get selected annotation for class
     *
     * @param \ReflectionClass $class
     * @param string $annotation
     * @return mixed
     */
    public function getClassAnnotation(\ReflectionClass $class, $annotation)
    {
        return $this->delegate->getClassAnnotation($class, $annotation);
    }

    /**
     * Get Annotations for method
     *
     * @param \ReflectionMethod $method
     * @return array
     */
    public function getMethodAnnotations(\ReflectionMethod $method)
    {
        $annotations = array();
        foreach ($this->delegate->getMethodAnnotations($method) as $annot) {
            $annotations[get_class($annot)] = $annot;
        }

        return $annotations;
    }

    /**
     * Get selected annotation for method
     *
     * @param \ReflectionMethod $method
     * @param string $annotation
     * @return mixed
     */
    public function getMethodAnnotation(\ReflectionMethod $method, $annotation)
    {
        return $this->delegate->getMethodAnnotation($method, $annotation);
    }

    /**
     * Get annotations for property
     *
     * @param \ReflectionProperty $property
     * @return array
     */
    public function getPropertyAnnotations(\ReflectionProperty $property)
    {
        $annotations = array();
        foreach ($this->delegate->getPropertyAnnotations($property) as $annot) {
            $annotations[get_class($annot)] = $annot;
        }

        return $annotations;
    }

    /**
     * Get selected annotation for property
     *
     * @param \ReflectionProperty $property
     * @param string $annotation
     * @return mixed
     */
    public function getPropertyAnnotation(\ReflectionProperty $property, $annotation)
    {
        return $this->delegate->getPropertyAnnotation($property, $annotation);
    }

    /**
     * Proxy all methods to the delegate.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->delegate, $method), $args);
    }
}