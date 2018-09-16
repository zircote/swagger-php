<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 */
class Property extends Schema
{
    /**
     * The key into Schema->properties array.
     *
     * @var string
     */
    public $property = UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        'OpenApi\Annotations\AdditionalProperties',
        'OpenApi\Annotations\Schema',
        'OpenApi\Annotations\JsonContent',
        'OpenApi\Annotations\XmlContent',
        'OpenApi\Annotations\Property',
        'OpenApi\Annotations\Items',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        'OpenApi\Annotations\Items' => 'items',
        'OpenApi\Annotations\Property' => ['properties', 'property'],
        'OpenApi\Annotations\AdditionalProperties' => 'additionalProperties',
        'OpenApi\Annotations\ExternalDocumentation' => 'externalDocs',
        'OpenApi\Annotations\Xml' => 'xml',
        'OpenApi\Annotations\Discriminator' => 'discriminator'
    ];
}
