<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace Swagger\Annotations;


/**
 * Class Examples
 *
 * @package Swagger\Annotations
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @Annotation
 */
class Examples extends AbstractAnnotation
{
    /**
     * Short description for the example.
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $summary;

    /**
     * Embedded literal example. The value field and externalValue field are
     * mutually exclusive. To represent examples of media types that cannot
     * naturally represented in JSON or YAML, use a string value to contain
     * the example, escaping where necessary.
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $description;

    /**
     * Embedded literal example.
     * The value field and externalValue field are mutually exclusive.
     * To represent examples of media types that cannot naturally represented
     * in JSON or YAML, use a string value to contain the example, escaping
     * where necessary.
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $value;

    /**
     * A URL that points to the literal example. This provides the
     * capability to reference examples that cannot easily be included
     * in JSON or YAML documents.
     * The value field and externalValue field are mutually exclusive.
     *
     * @var string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $externalValue;

    public static $_types = [
        'summary' => 'string',
        'value' => 'string',
        'description' => 'string',
        'externalValue' => 'string',
    ];

    public static $_required = ['summary'];

    public static $_parents = [
        'Swagger\Annotations\Components',
        'Swagger\Annotations\Parameter',
        'Swagger\Annotations\MediaType',
    ];
}