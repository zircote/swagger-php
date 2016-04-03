<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Analysis;
use Swagger\Annotations\AbstractAnnotation;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Dynamic;
use Swagger\Annotations\Property;

/**
 *
 */
class ExtractDynamic
{
    const PRE = "dynamic-definition-";

    private static $definitions = [];
    private static $dynamics = [];
    private $_inc = 0;
    private $created = [];

    public function __invoke(Analysis $analysis)
    {

        //get all the dynamic definitions.
        /** @var Dynamic $dynamic */
        foreach (self::$dynamics as $dynamic) { //for each register dynamic json

            $definition = self::$definitions[$dynamic->use];

            $object = get_object_vars(clone $definition);

            $name = $this->cachedName($dynamic);

            if ($name == false) { //if it doesnt exist then create the values
                $list = $this->read($object);

                //set each value in the definition to the ref
                foreach ($dynamic->stringRefs() as $key => $value) {
                    $list[$key] = $value;
                }

                //define the name
                $name = self::PRE . $this->_inc++;

                $object['definition'] = $name;

                $analysis->addAnnotation(new Definition($object), $object['_context']);

                //caches the definition incase it is used again
                $this->cacheDefinition($name, $dynamic);
            }

            //sets the reference to the dynamic definition
            $dynamic->setRef($name);

        }

    }

    /**
     * Caches the definition in case the same environment is created before.
     *
     * @param $name
     * @param Dynamic $dynamic
     */
    public function cacheDefinition($name, Dynamic $dynamic)
    {
        $this->created[$this->hash($dynamic)] = $name;
    }

    /**
     * Creates a hash for the given input values.
     *
     * @param Dynamic $dynamic
     * @return string
     */
    private function hash(Dynamic $dynamic)
    {
        $data = [$dynamic->use, $dynamic->stringRefs()];
        return md5(serialize($data));
    }

    /**
     * Gets the cached names depending on what values.
     *
     * @param Dynamic $dynamic
     * @return string|bool
     */
    public function cachedName(Dynamic $dynamic)
    {
        $hash = $this->hash($dynamic);
        return isset($this->created[$hash])? $this->created[$hash]: false;
    }


    /**
     * Reads the object and returns a reference to all of the values surrounded with {{}}
     *
     * @param array|\stdClass $obj
     * @return array
     */
    private function read($obj)
    {
        $array = [];

        foreach ($obj as $key => &$value) {
            if (is_array($value) || $value instanceof AbstractAnnotation || $value instanceof \stdClass) {
                $array = array_merge($array, $this->read($value));
            } else if (preg_match('/\{\{(.*?)\}\}/', $value, $matches)) {
                $id = $matches[1];
                $array[$id] = &$value;
            }
        }

        return $array;
    }

    /**
     * Adds the value to the dynamic value.
     *
     * @param $dynamic
     */
    public static function addDynamic($dynamic)
    {
        self::$dynamics[] = $dynamic;
    }

    /**
     * Adds the value to the definitions value
     *
     * @param $key
     * @param $definition
     */
    public static function addDefinition($key, $definition)
    {
        self::$definitions[$key] = $definition;
    }
}
