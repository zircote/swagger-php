<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Analysis;
use OpenApi\Undefined;

/**
 * @Annotation
 */
class Examples extends AbstractAnnotation
{
    /**
     * The relative or absolute path to an example.
     *
     * @see [Reference Object](https://spec.openapis.org/oas/v3.1.1.html#reference-object)
     *
     * @var string|class-string|object
     */
    public $ref = Undefined::UNDEFINED;

    /**
     * The key into <code>#/components/examples</code>.
     *
     * @var string
     */
    public $example = Undefined::UNDEFINED;

    /**
     * Short description for the example.
     *
     * @var string
     */
    public $summary = Undefined::UNDEFINED;

    /**
     * Embedded literal example.
     *
     * The value field and externalValue field are mutually exclusive.
     *
     * To represent examples of media types that cannot naturally be represented
     * in JSON or YAML, use a string value to contain the example, escaping where necessary.
     *
     * @var string
     */
    public $description = Undefined::UNDEFINED;

    /**
     * Embedded literal example.
     *
     * The value field and externalValue field are mutually exclusive.
     *
     * To represent examples of media types that cannot naturally be represented
     * in JSON or YAML, use a string value to contain the example, escaping where necessary.
     *
     * @var int|string|array
     */
    public $value = Undefined::UNDEFINED;

    /**
     * An URL that points to the literal example.
     *
     * This provides the capability to reference examples that cannot easily be included
     * in JSON or YAML documents.
     *
     * The value field and externalValue field are mutually exclusive.
     *
     * @var string
     */
    public $externalValue = Undefined::UNDEFINED;

    public static $_types = [
        'summary' => 'string',
        'description' => 'string',
        'externalValue' => 'string',
    ];

    public static $_parents = [
        AdditionalProperties::class,
        Components::class,
        Items::class,
        Schema::class,
        Parameter::class,
        PathParameter::class,
        Property::class,
        MediaType::class,
        Header::class,
        JsonContent::class,
        XmlContent::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];

    #[\Override]
    public function validate(?Analysis $analysis = null, string $version = OpenApi::DEFAULT_VERSION, ?object $context = null): bool
    {
        $isValid = parent::validate($analysis, $version, $context);

        if (!Undefined::isDefault($this->value) && !Undefined::isDefault($this->externalValue)) {
            $this->_context->logger->warning($this->identity() . ' value and externalValue are mutually exclusive in ' . $this->_context);
            $isValid = false;
        }

        if ($this->isSchemaExample() && Undefined::isDefault($this->value)) {
            $this->_context->logger->warning('Missing required field "value" for ' . $this->identity() . ' in ' . $this->_context);
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Whether this sits in the JSON Schema <code>examples</code> keyword rather than in a map of
     * Example Objects.
     *
     * The parent says which: one that collects examples without a key-field holds a list of
     * values, where only <code>value</code> survives and the rest of an Example Object — the key,
     * <code>summary</code>, <code>description</code>, <code>externalValue</code> — has nowhere
     * to go.
     */
    protected function isSchemaExample(): bool
    {
        $parent = $this->_context->nested;
        if (!$parent instanceof AbstractAnnotation || !$nested = $parent->matchNested($this)) {
            return false;
        }

        return is_array($nested->value) && count($nested->value) === 1;
    }
}
