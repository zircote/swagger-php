<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\PathItem;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Logger;

/**
 * Copy the annotated properties from parent classes;
 */
class HandleReferences
{
    /**
     * @var array The allowed imports in order of import
     */
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
     * @param \OpenApi\Analysis $analysis
     */
    private function getAllImports(Analysis $analysis)
    {
        // for all importable content
        foreach ($this->import_in_order as $propertyName => $importName) {
            // initialise the import name
            $this->references[$importName] = [];
            $this->head_references[$importName] = [];

            if (!is_null($analysis->openapi->$importName)) {
                foreach ($analysis->openapi->$importName as $item) {
                    //if that identified value exists, or if the name isn't set then give blank id
                    if (!is_null($item->$propertyName) && isset($this->references[$importName][$item->$propertyName])) {
                        Logger::notice("$propertyName is already defined for object \"" . get_class($item) . '" in ' . $item->_context);
                    } else {
                        $this->references[$importName][$item->$propertyName] = $this->link($item);
                        //                        Logger::notice("$propertyName is NULL on object \"" . get_class($item) . '" in ' . $item->_context);
                    }
                }
            }
        }

        // All of the paths in the openapi, we need to iterate across
        if ($analysis->openapi->paths !== UNDEFINED) {
            foreach ($analysis->openapi->paths as $path) {
                foreach ($path as $propertyName => $value) {
                    if ($value instanceof Operation && !is_null($value->responses)) {
                        //load each importable item if it is set
                        $this->loadResponses($value);
                        $this->loadParameters($value);
                        $this->loadSchemas($value);
                    }
                }
            }
        }
    }

    /**
     * Creates the Linked list array item.
     *
     * @param  $response
     * @return array
     */
    private function link($response)
    {
        return [null, $response, []];
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
     * Checks the syntax of the string to make sure it starts with a $
     *
     * @param  $string
     * @return int
     */
    private function checkSyntax($string)
    {
        return isset($string) && preg_match('/^\$/', $string);
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
        foreach ($params as $item) {
            if (is_array($item->properties)) {
                $array = array_merge($array, $item->properties);
            }
            if ($this->checkSyntax($item->ref)) {
                $this->references[$this->import_in_order['definition']][] = $this->link($item);
            }
        }

        // nest the next loop
        if (count($array)) {
            $this->propertyRetrieve($array);
        }
    }

    /**
     * Maps the response to each parent child.
     */
    private function mapReferences()
    {
        foreach ($this->import_in_order as $key => $import_name) {
            foreach ($this->references[$import_name] as &$data) {
                $item = $data[1];

                $this->recursiveMap($item, $data);

                if (!$this->checkSyntax($item->ref)) {
                    $this->head_references[$import_name][] = &$data;
                }
            }
        }
    }

    /**
     * Recursively iterates over the item. Getting all possible dynamic references from the object and its children.
     * For each reference it will map its array data to the reference name.
     *
     * @param mixed $item
     * @param null  $data
     */
    private function recursiveMap($item, &$data = null)
    {
        if (!is_object($item) && !is_array($item)) {
            return;
        }

        if (is_object($item)) {
            if (property_exists($item, 'ref') && $this->checkSyntax($item->ref)) {
                $params = explode("/", $item->ref);
                $this->loadParent($data, strtolower($params[1]), $params[2], $item);
            }
        }

        foreach ($item as $key => $value) {
            if ($key == '_context') {
                continue;
            }
            $this->recursiveMap($value);
        }
    }

    /**
     * Combines all of the links, adding each reference to their respective parents.
     * If the parent exists.
     *
     * @param $child
     * @param $type
     * @param $parent_name
     * @param $item
     */
    private function loadParent(&$child, $type, $parent_name, $item)
    {
        if (!isset($child)) {
            $child = $this->link($item);
        }

        if (isset($this->references[$type]) && isset($this->references[$type][$parent_name])) {
            //link the parent
            $child[0] = &$this->references[$type][$parent_name];
            //add to list of children
            $this->references[$type][$parent_name][2][] = &$child;
        } else {
            Logger::notice("Unable to find the $type reference \"$parent_name\" in " . $item->_context);
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
     * @param array       $queue
     * @param $current_key
     */
    private function iterateQueue(&$queue, $current_key)
    {
        $item = array_pop($queue);
        $queue = array_merge($queue, $item[2]);

        $response = $item[1];
        $parent = $item[0];

        //Reset the ref
        $response->ref = null;

        if (is_null($parent)) {
            return;
        }

        $parent_obj = $parent[1];

        foreach ($parent_obj as $key => $value) {
            if ($key == 'schema') {
                if (!is_null($value)) {
                    $response->schema = $response->schema ?: new Schema([]);
                    $this->importSchema($value, $response->schema);
                }
            } elseif (!in_array($key, array_keys($this->import_in_order)) && property_exists($response, $key)) {
                if (is_array($value)) {
                    $response->$key = array_merge($value, $response->$key ?: []);
                } elseif (!isset($response->$key) && $key != $current_key || $key == 'ref') {
                    $response->$key = $value;
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

        // add all in a temporary array
        if (!is_null($child->properties)) {
            foreach ($child->properties as $value) {
                $temp[$value->property] = $value;
            }
        }

        // reset the properties
        $child->properties = [];

        foreach ($parent as $key => $value) {
            if ($key == 'properties' && is_array($value)) {
                foreach ($value as $property) {
                    // if the parent property exists and the child property with the same name exists,
                    // then will use the child property
                    if (isset($temp[$property->property])) {
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

        $child->properties = $child->properties ?: [];

        //now we need to just add the ones in the temp array back in.
        foreach ($temp as $name => $temp_item) {
            $found = false;

            // if there are no properties then skip
            foreach ($child->properties as $property) {
                if ($property->property == $name) {
                    $found = true;
                    break;
                }
            }

            //if it doesn't already exist then add it
            if (!$found) {
                $child->properties[] = $temp_item;
            }
        }
    }
}
