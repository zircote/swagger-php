<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;
use Swagger\Processors\ExtractDynamic;

/**
 * @Annotation
 */
class Dynamic extends AbstractAnnotation
{
    /**
     * The key into Swagger->definitions array.
     * @var string
     */
    public $use;

    /**
     * @var
     */
    public $refs;

    /** @inheritdoc */
    public static $_types = [
        'use' => 'string',
        'refs' => 'array'
    ];

    private $ref_value = "";

    /** @inheritdoc */
    public static $_parents = [];

    /**
     * Stringified version of the refs
     *
     * @var
     */
    private $string_refs;

    public function __construct(array $properties)
    {
        parent::__construct($properties);
        ExtractDynamic::addDynamic($this); //add it to the list
    }

    /**
     * Just return the new Dynamically Created Definition
     *
     * @return string
     */
    public function __toString()
    {
        return $this->ref_value;
    }

    /**
     * Set the ref for the reference
     *
     * @param $string
     */
    public function setRef($string) {
        $this->ref_value = "#/definitions/" . $string;
    }

    /**
     * Just return the reference for the jsonSerialize
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->ref_value;
    }

    /**
     * Returns the refs as a string
     *
     * @return array
     */
    public function string_refs() {
        if (!is_null($this->string_refs)) return $this->string_refs;

        $array = [];
        foreach ($this->refs as $key => $value) {
            $array[$key] = "$value";
        }

        return $this->string_refs = $array;
    }
}
