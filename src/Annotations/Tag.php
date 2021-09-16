<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * A "Tag Object":  https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#tagObject.
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
abstract class AbstractTag extends AbstractAnnotation
{
    /**
     * The name of the tag.
     *
     * @var string
     */
    public $name = Generator::UNDEFINED;

    /**
     * A short description for the tag. GFM syntax can be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * Additional external documentation for this tag.
     *
     * @var ExternalDocumentation
     */
    public $externalDocs = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['name'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'name' => 'string',
        'description' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        OpenApi::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        ExternalDocumentation::class => 'externalDocs',
        Attachable::class => ['attachables'],
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class Tag extends AbstractTag
    {
        public function __construct(
            array $properties = [],
            string $name = Generator::UNDEFINED,
            string $description = Generator::UNDEFINED,
            $externalDocs = Generator::UNDEFINED,
            $x = Generator::UNDEFINED
        ) {
            parent::__construct($properties + [
                    'name' => $name,
                    'description' => $description,
                    'value' => $this->combine($externalDocs),
                    'x' => $x,
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Tag extends AbstractTag
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
