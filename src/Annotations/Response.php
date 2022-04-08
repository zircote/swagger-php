<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Describes a single response from an API Operation, including design-time,
 * static links to operations based on the response.
 *
 * @see [OAI Response Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#response-object)
 *
 * @Annotation
 */
class Response extends AbstractAnnotation
{
    /**
     * @see [Using refs](https://swagger.io/docs/specification/using-ref/)
     *
     * @var string
     */
    public $ref = Generator::UNDEFINED;

    /**
     * The key into Operations->responses array.
     *
     * A HTTP status code or <code>default</code>.
     *
     * @var string|int
     */
    public $response = Generator::UNDEFINED;

    /**
     * A short description of the response.
     *
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * Maps a header name to its definition.
     *
     * RFC7230 states header names are case insensitive.
     *
     * If a response header is defined with the name "Content-Type", it shall be ignored.
     *
     * @see [RFC7230](https://tools.ietf.org/html/rfc7230#page-22)
     *
     * @var Header[]
     */
    public $headers = Generator::UNDEFINED;

    /**
     * A map containing descriptions of potential response payloads.
     *
     * The key is a media type or media type range and the value describes it.
     *
     * For responses that match multiple keys, only the most specific key is applicable;
     * e.g. <code>text/plain</code> overrides <code>text/*</code>.
     *
     * @var MediaType[]
     */
    public $content = Generator::UNDEFINED;

    /**
     * A map of operations links that can be followed from the response.
     *
     * The key of the map is a short name for the link, following the naming constraints of the names for Component
     * Objects.
     *
     * @var array
     */
    public $links = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'description' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        MediaType::class => ['content', 'mediaType'],
        Header::class => ['headers', 'header'],
        Link::class => ['links', 'link'],
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Components::class,
        Operation::class,
        Get::class,
        Post::class,
        Put::class,
        Patch::class,
        Delete::class,
        Head::class,
        Options::class,
        Trace::class,
    ];

    /**
     * @inheritdoc
     */
    public function validate(array $stack = [], array $skip = [], string $ref = '', $context = null): bool
    {
        $valid = parent::validate($stack, $skip, $ref, $context);

        if (Generator::isDefault($this->description) && Generator::isDefault($this->ref)) {
            $this->_context->logger->warning($this->identity() . ' One of description or ref is required');
            $valid = false;
        }

        return $valid;
    }
}
