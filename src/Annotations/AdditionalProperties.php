<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace Swagger\Annotations;

/**
 * Class AdditionalProperties
 *
 * @package Swagger\Annotations
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @Annotation
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
