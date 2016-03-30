<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Operation;
use Swagger\Annotations\Path;
use Swagger\Annotations\Property;
use Swagger\Annotations\Response;
use Swagger\Annotations\Schema;
use Swagger\Analysis;

/**
 * Copy the annotated properties from parent classes;
 */
class HandleReferences
{
    /** @var array The allowed imports in order of import */
    private $import_in_order = [
        'parameter' => 'parameters',
        'definition' => 'definitions',
        'response' => 'responses'
    ];

    private $references = [];
    private $head_references = [];

    public function __invoke(Analysis $analysis)
    {
        $this->getAllImports($analysis);
        $this->mapReferences();
        $this->importReferences();
    }

    /**
     * Gets all the possible importable objects and adds them to the lists.
     *
     * @param Analysis $analysis
     */
    private function getAllImports(Analysis $analysis)
    {
        //for all importable content
        foreach ($this->import_in_order as $key => $import_name) {
            //initialise the import name
            $this->references[$import_name] = [];
            $this->head_references[$import_name] = [];

            if (!is_null($analysis->swagger->$import_name)) {
                /** @var Response $item */
                foreach ($analysis->swagger->$import_name as $item) {
                    //if that identified value exists, or if the name isn't set then give blank id
                    if (isset($this->references[$import_name][$item->$key]) || is_null($item->$key)) {
                        $this->references[$import_name][] = $this->link($item);
                    } else { //else assign to id
                        $this->references[$import_name][$item->$key] = $this->link($item);
                    }
                }
            }
        }

        if (!is_null($analysis->swagger->paths)) {
            /** @var Path $path */
            foreach ($analysis->swagger->paths as $path) {
                foreach ($path as $key => $value) {
                    if ($value instanceof Operation && !is_null($value->responses)) {
                        //load each importable item if it is set
                        if (isset($this->import_in_order['response'])) {
                            $this->loadResponses($value);
                        }
                        if (isset($this->import_in_order['parameter'])) {
                            $this->loadParameters($value);
                        }
                        if (isset($this->import_in_order['definition'])) {
                            $this->loadSchemas($value);
                        }
                    }
                }
            }
        }
    }

    /**
     * Loads all the responses into the mapping
     *
     * @param Operation $operation
     */
    private function loadResponses(Operation $operation)
    {
        if (!is_null($operation->responses)) {
            foreach ($operation->responses as $item) {
                if ($this->checkSyntax($item->ref)) {
                    $this->references[$this->import_in_order['response']][] = $this->link($item);
                }
            }
        }
    }

    /**
     * Loads all the parameters into the mapping
     *
     * @param Operation $operation
     */
    private function loadParameters(Operation $operation)
    {
        if (!is_null($operation->parameters)) {
            foreach ($operation->parameters as $item) {
                if ($this->checkSyntax($item->ref)) {
                    $this->references[$this->import_in_order['parameter']][] = $this->link($item);
                }
            }
        }
    }

    /**
     * Loads all the schemas into the mapping
     *
     * @param Operation $operation
     */
    private function loadSchemas(Operation $operation)
    {
        if (!is_null($operation->responses)) {
            /** @var Response $item */
            foreach ($operation->responses as $item) {
                if (!is_null($item->schema)) {
                    $this->propertyRetrieve([$item->schema]);
                }
            }
        }
    }

    /**
     * Retrieves all the sub properties
     *
     * @param array $params
     */
    private function propertyRetrieve(array $params)
    {
        $array = [];

        /** @var Schema $item */
        foreach ($params as $item) {
            if (!is_null($item->properties)) {
                $array = array_merge($array, $item->properties);
            }
            if ($this->checkSyntax($item->ref)) {
                $this->references[$this->import_in_order['definition']][] = $this->link($item);
            }
        }

        //nest the next loop
        if (count($params)) {
            $this->propertyRetrieve($array);
        }
    }

    /**
     * Creates the Linked list array item.
     *
     * @param $response
     * @return array
     */
    private function link($response)
    {
        return [null, $response, []];
    }

    /**
     * Maps the response to each parent child.
     */
    private function mapReferences()
    {
        foreach ($this->import_in_order as $key => $import_name) {
            foreach ($this->references[$import_name] as &$data) {
                /** @var Response $item */
                $item = $data[1];


                if (!isset($item->ref)) {
                    $this->head_references[$import_name][] = &$data;
                } else if ($this->checkSyntax($item->ref)) {
                    $params = explode("/", $item->ref);

                    $this->loadParent($data, strtolower($params[1]), $params[2]);
                }
            }
        }
    }

    /**
     * Checks the syntax of the string to make sure it starts with a $
     *
     * @param $string
     * @return int
     */
    private function checkSyntax($string)
    {
        return preg_match('/^\$/', $string);
    }

    /**
     * Links the child with the parent
     *
     * @param $child
     * @param $type
     * @param $parent_name
     */
    private function loadParent(&$child, $type, $parent_name)
    {
        if (isset($this->references[$type]) && isset($this->references[$type][$parent_name])) {
            //link the parent
            $child[0] = &$this->references[$type][$parent_name];
            //add to list of children
            $this->references[$type][$parent_name][2][] = &$child;
        }
    }

    /**
     * Imports the references from all of the responses
     */
    private function importReferences()
    {
        foreach ($this->import_in_order as $key => $import_name) {
            //get the list to import from
            $queue = $this->head_references[$import_name];

            //while has items in the queue
            while (count($queue)) {
                $this->iterateQueue($queue, $key);
            }
        }
    }

    /**
     * Iterates the pending queue, popping the first element of the list.
     *
     * @param array $queue
     * @param $current_key
     */
    private function iterateQueue(&$queue, $current_key)
    {
        $item = array_pop($queue);

        $queue = array_merge($queue, $item[2]);

        /** @var Response $response */
        $response = $item[1];
        /** @var Response $parent_response */
        $parent = $item[0];

        //Reset the ref
        $response->ref = null;

        if (!is_null($parent)) {
            $parent_response = $parent[1];
            foreach ($parent_response as $key => $value) {
                if ($key == "schema") {
                    if (!is_null($value)) {
                        if (is_null($response->schema)) {
                            $response->schema = new Schema([]);
                        }
                        $this->importSchema($value, $response->schema);
                    }
                } else if ($key != "response") {
                    if (is_array($value)) {
                        $response->$key = array_merge($response->$key?: [], $parent_response->$key);
                    } else if (!isset($response->$key) && $key != $current_key) {
                        $response->$key = $parent_response->$key;
                    }
                }
            }
        }
    }

    /**
     * Imports the schema
     *
     * @param Schema $parent
     * @param Schema $child
     */
    private function importSchema(Schema $parent, Schema $child)
    {
        $temp = [];

        //add all in a temporary array
        if (!is_null($child->properties)) {
            foreach ($child->properties as $key => $value) {
                $temp[$value->property] = $value;
            }
        }

        //reset the properties
        $child->properties = [];

        foreach ($parent as $key => $value) {
            if ($key == "properties") {
                /** @var Property[] $value */
                foreach ($value as $property) {
                    if ($this->isEmpty($property) && isset($temp[$property->property])) { //if it has the same field
                        $child->properties[] = $temp[$property->property];
                        unset($temp[$property->property]);
                    } else {
                        $child->properties[] = $property;
                    }
                }
            } else {
                $child->$key = $parent->$key;
            }
        }

        //now we need to just add the ones in the temp array back in.
        foreach ($temp as $name => $temp_item) {
            $found = false;
            foreach ($child->properties as $property) {
                if ($property->property == $name) {
                    $found = true;
                }
            }
            //if it doesn't already exist then add it
            if (!$found) {
                $child->properties[] = $temp_item;
            }
        }
    }

    /**
     * Checks if the value is empty.
     *
     * @param Property $property
     * @return bool
     */
    private function isEmpty(Property $property)
    {
        return !isset($property->type)
        && !isset($property->description)
        && $property->default == \SWAGGER\UNDEFINED;
    }
}
