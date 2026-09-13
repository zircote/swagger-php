<?php declare(strict_types=1);
/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Analysis;
use OpenApi\Undefined;

/**
 * @see [Header Object](https://spec.openapis.org/oas/v3.1.1.html#header-object)
 *
 * @Annotation
 */
class Header extends AbstractAnnotation
{
    /**
     * The relative or absolute path to the endpoint.
     *
     * @see [Reference Object](https://spec.openapis.org/oas/v3.1.1.html#reference-object)
     *
     * @var string|class-string|object
     */
    public $ref = Undefined::UNDEFINED;

    /**
     * @var string
     */
    public $header = Undefined::UNDEFINED;

    /**
     * A brief description of the parameter.
     *
     * This could contain examples of use.
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     */
    public $description = Undefined::UNDEFINED;

    /**
     * @var bool
     */
    public $required = Undefined::UNDEFINED;

    /**
     * Describes how the header value will be serialized.
     *
     * Headers support only the "simple" style; it is also the default.
     *
     * @var string
     */
    public $style = Undefined::UNDEFINED;

    /**
     * When this is true, header values of type array or object generate a single header
     * whose value is a comma-separated list of the array items or key-value pairs of the map.
     *
     * The default value is false.
     *
     * @var bool
     */
    public $explode = Undefined::UNDEFINED;

    /**
     * Schema object.
     *
     * @var Schema
     */
    public $schema = Undefined::UNDEFINED;

    /**
     * Example of the header.
     *
     * The example should match the specified schema if present.
     * The example object is mutually exclusive of the examples object.
     * Furthermore, if referencing a schema which contains an example, the example value shall override the example provided by the schema.
     *
     * @var mixed
     */
    public $example = Undefined::UNDEFINED;

    /**
     * Examples of the header.
     *
     * Each example should match the specified schema if present.
     * The examples object is mutually exclusive of the example object.
     * Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.
     *
     * @var array<Examples>
     */
    public $examples = Undefined::UNDEFINED;

    /**
     * A map containing the representations for the header.
     *
     * The key is the media type and the value describes it.
     * The map must only contain one entry.
     *
     * The content map is mutually exclusive of the schema property.
     *
     * @var array<MediaType>|JsonContent|XmlContent|Attachable
     */
    public $content = Undefined::UNDEFINED;

    /**
     * Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.
     *
     * @var bool
     */
    public $deprecated = Undefined::UNDEFINED;

    /**
     * Sets the ability to pass empty-valued parameters.
     *
     * This is valid only for query parameters and allows sending a parameter with an empty value.
     *
     * Default value is false.
     *
     * If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue SHALL be ignored.
     *
     * @var bool
     */
    public $allowEmptyValue = Undefined::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['header'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'header' => 'string',
        'description' => 'string',
        'style' => ['simple'],
        'explode' => 'boolean',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Schema::class => 'schema',
        Examples::class => ['examples', 'example'],
        MediaType::class => ['content', 'mediaType'],
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Encoding::class,
        Components::class,
        Response::class,
    ];

    #[\Override]
    public function validate(?Analysis $analysis = null, string $version = OpenApi::DEFAULT_VERSION, ?object $context = null): bool
    {
        $isValid = parent::validate($analysis, $version, $context);

        if (Undefined::isDefault($this->ref)) {
            $hasSchema = !Undefined::isDefault($this->schema);
            $hasContent = !Undefined::isDefault($this->content);
            if ($hasSchema && $hasContent) {
                $this->_context->logger->warning($this->identity() . ' schema and content are mutually exclusive in ' . $this->_context);
                $isValid = false;
            } elseif (!$hasSchema && !$hasContent) {
                $this->_context->logger->warning($this->identity() . ' requires one of "schema" or "content" in ' . $this->_context);
                $isValid = false;
            }
        }

        return $isValid;
    }
}
