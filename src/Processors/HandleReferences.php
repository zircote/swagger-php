<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Get;
use Swagger\Annotations\Path;
use Swagger\Annotations\Post;
use Swagger\Annotations\Property;
use Swagger\Annotations\Put;
use Swagger\Annotations\Response;
use Swagger\Annotations\Schema;
use Swagger\Annotations\Swagger;
use Swagger\Annotations\Definition;
use Swagger\Analysis;

/**
 * Copy the annotated properties from parent classes;
 */
class HandleReferences
{

    private $responses = [];
    private $head_responses = [];

    public function __invoke(Analysis $analysis)
    {
        /** @var Response $response */
        foreach ($analysis->swagger->responses as $response) {
            $this->responses[$response->response] = [null, $response, []];
        }

        /** @var Get|Put|Post $path */
        foreach ($analysis->swagger->paths as $path) {
            foreach ($path->responses as $response) {
                $this->responses[$response->response] = $this->link($response);
            }
        }

        $this->mapResponses();
        $this->importReferences();
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
    private function mapResponses()
    {
        foreach ($this->responses as &$data) {
            /** @var Response $response */
            $response = $data[1];

            if (preg_match('/^\$/',$response->ref)) {
                $params = explode("/", strtolower($response->ref));

                $this->loadParent($data, $params[1], $params[2]);
            } else {
                $this->head_responses[] = &$data;
            }
        }
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
        if (isset($this->$type) && isset($this->{$type}[$parent_name])) {
            //link the parent
            $child[0] = &$this->{$type}[$parent_name];
            //add to list of children
            $this->{$type}[$parent_name][2][] = &$child;
        }
    }

    /**
     * Imports the references from all of the responses
     */
    private function importReferences()
    {
        $queue = $this->head_responses;

        //while has items in the queue
        while (count($queue)) {
            $this->iterateQueue($queue);
        }
    }

    /**
     * Iterates the pending queue, popping the first element of the list.
     *
     * @param array $queue
     */
    private function iterateQueue(&$queue)
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
                    $this->importSchema($value, $response->schema);
                } else if ($key != "response") {
                    if (is_array($value)) {
                        $response->$key = array_merge($response->$key, $parent_response->$key);
                    } else if (!isset($response->$key)) {
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
        foreach ($child->properties as $key => $value) {
            $temp[$value->property] = $value;
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
                if ($property->property != $name) {
                    $found = true;
                }
            }
            //if it doesn't already exist then add it
            if (!$found) $child->properties[] = $temp_item;
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
