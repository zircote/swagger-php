<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;
use Swagger\Processors\ExtractDynamic;

/**
 * @Annotation
 */
class DynamicDefinition extends Schema
{

    /**
     * The key into Swagger->definitions array.
     * @var string
     */
    public $definition;

    /** @inheritdoc */
    public static $_types = [
        'definition' => 'string'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Swagger'
    ];

    public function __construct(array $properties)
    {
        parent::__construct($properties);
        ExtractDynamic::addDefinition($this->definition, $this);
    }

}
