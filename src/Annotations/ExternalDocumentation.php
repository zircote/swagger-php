<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Allows referencing an external resource for extended documentation.
 *
 * A "External Documentation Object": https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#external-documentation-object
 *
 * @Annotation
 */
abstract class AbstractExternalDocumentation extends AbstractAnnotation
{
    /**
     * A short description of the target documentation. GFM syntax can be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * The URL for the target documentation.
     *
     * @var string
     */
    public $url = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'description' => 'string',
        'url' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_required = ['url'];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        OpenApi::class,
        Tag::class,
        Schema::class,
        AdditionalProperties::class,
        Property::class,
        Operation::class,
        Get::class,
        Post::class,
        Put::class,
        Delete::class,
        Patch::class,
        Head::class,
        Options::class,
        Trace::class,
        Items::class,
        JsonContent::class,
        XmlContent::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class ExternalDocumentation extends AbstractExternalDocumentation
    {
        public function __construct(
            array $properties = [],
            string $description = Generator::UNDEFINED,
            string $url = Generator::UNDEFINED,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'description' => $description,
                    'url' => $url,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class ExternalDocumentation extends AbstractExternalDocumentation
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
