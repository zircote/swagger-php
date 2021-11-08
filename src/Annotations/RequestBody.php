<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * A "Response Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#requestBodyObject.
 *
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 *
 * @Annotation
 */
abstract class AbstractRequestBody extends AbstractAnnotation
{
    public $ref = Generator::UNDEFINED;

    /**
     * Request body model name.
     *
     * @var string
     */
    public $request = Generator::UNDEFINED;

    /**
     * A brief description of the parameter.
     * This could contain examples of use.
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * Determines whether this parameter is mandatory.
     * If the parameter location is "path", this property is required and its value must be true.
     * Otherwise, the property may be included and its default value is false.
     *
     * @var bool
     */
    public $required = Generator::UNDEFINED;

    /**
     * The content of the request body.
     * The key is a media type or media type range and the value describes it. For requests that match multiple keys,
     * only the most specific key is applicable. e.g. text/plain overrides text/*.
     *
     * @var MediaType[]
     */
    public $content = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'description' => 'string',
        'required' => 'boolean',
        'request' => 'string',
    ];

    public static $_parents = [
        Components::class,
        Delete::class,
        Get::class,
        Head::class,
        Operation::class,
        Options::class,
        Patch::class,
        Post::class,
        Trace::class,
        Put::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        MediaType::class => ['content', 'mediaType'],
        Attachable::class => ['attachables'],
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER)]
    class RequestBody extends AbstractRequestBody
    {
        public function __construct(
            array $properties = [],
            string $description = Generator::UNDEFINED,
            ?bool $required = null,
            $content = Generator::UNDEFINED,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'description' => $description,
                    'required' => $required ?? Generator::UNDEFINED,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($content, $attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class RequestBody extends AbstractRequestBody
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
