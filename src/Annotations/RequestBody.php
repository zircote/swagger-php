<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\XmlContent;
use OpenApi\Generator;

/**
 * Describes a single request body.
 *
 * @see [Request Body Object](https://spec.openapis.org/oas/v3.1.1.html#request-body-object)
 *
 * @Annotation
 */
class RequestBody extends AbstractAnnotation
{
    /**
     * The relative or absolute path to a request body.
     *
     * @see [Reference Object](https://spec.openapis.org/oas/v3.1.1.html#reference-object)
     *
     * @var string|class-string|object
     */
    public $ref = Generator::UNDEFINED;

    /**
     * The key into Components->requestBodies array.
     *
     * @var string
     */
    public $request = Generator::UNDEFINED;

    /**
     * A brief description of the parameter.
     *
     * This could contain examples of use.
     *
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * Determines whether this parameter is mandatory.
     *
     * If the parameter location is "path", this property is required and its value must be true.
     * Otherwise, the property may be included and its default value is false.
     *
     * @var bool
     */
    public $required = Generator::UNDEFINED;

    /**
     * The content of the request body.
     *
     * The key is a media type or media type range and the value describes it. For requests that match multiple keys,
     * only the most specific key is applicable. e.g. text/plain overrides text/*.
     *
     * @var array<MediaType|JsonContent|XmlContent>|MediaType|JsonContent|XmlContent|Attachable
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

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        unset($data->request);

        return $data;
    }
}
