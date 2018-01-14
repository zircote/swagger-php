<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class Property extends Schema
{
    /**
     * The key into Schema->properties array.
     * @var string
     */
    public $property;

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\AdditionalProperties',
        'Swagger\Annotations\Schema',
        'Swagger\Annotations\JsonContent',
        'Swagger\Annotations\XmlContent',
        'Swagger\Annotations\Property',
        'Swagger\Annotations\Items',
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Items' => 'items',
        'Swagger\Annotations\Property' => ['properties', 'property'],
        'Swagger\Annotations\ExternalDocumentation' => 'externalDocs',
        'Swagger\Annotations\Xml' => 'xml',
    ];
}
