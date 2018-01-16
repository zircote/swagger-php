<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 *
 * A "Tag Object":  https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#tagObject
 */
class Tag extends AbstractAnnotation
{
    /**
     * The name of the tag.
     * @var string
     */
    public $name;

    /**
     * A short description for the tag. GFM syntax can be used for rich text representation.
     * @var string
     */
    public $description;

    /**
     * Additional external documentation for this tag.
     * @var ExternalDocumentation
     */
    public $externalDocs;

    /** @inheritdoc */
    public static $_required = ['name'];

    /** @inheritdoc */
    public static $_types = [
        'name' => 'string',
        'description' => 'string',
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\OpenApi'
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\ExternalDocumentation' => 'externalDocs'
    ];
}
