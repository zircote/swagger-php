<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION", "PROPERTY"})
 * Class AdditionalProperties
 * Additional properties.
 * https://swagger.io/docs/specification/data-models/dictionaries/
 *
 * @package Swagger\Annotations
 */
class AdditionalProperties extends Schema
{
    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Schema'
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Items' => 'items',
        'Swagger\Annotations\Property' => ['properties', 'property'],
        'Swagger\Annotations\ExternalDocumentation' => 'externalDocs',
        'Swagger\Annotations\Xml' => 'xml',
    ];
}
