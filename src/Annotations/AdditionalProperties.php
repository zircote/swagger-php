<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 */
class AdditionalProperties extends Schema
{
    /**
     * @inheritdoc
     */
    public static $_parents = [
        'OpenApi\Annotations\Schema',
        'OpenApi\Annotations\Property'
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        'OpenApi\Annotations\Items' => 'items',
        'OpenApi\Annotations\Property' => ['properties', 'property'],
        'OpenApi\Annotations\ExternalDocumentation' => 'externalDocs',
        'OpenApi\Annotations\Xml' => 'xml',
    ];
}
