<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Undefined;

/**
 * A single encoding definition applied to a single schema property.
 *
 * @see [Encoding Object](https://spec.openapis.org/oas/v3.1.0.html#encoding-object)
 *
 * @Annotation
 */
class Encoding extends AbstractAnnotation
{
    /**
     * The property name to which the encoding applies.
     *
     * @var string
     */
    public $property = Undefined::UNDEFINED;

    /**
     * The content type.
     *
     * @var string
     */
    public $contentType = Undefined::UNDEFINED;

    /**
     * Additional headers.
     *
     * @var list<Header>
     */
    public $headers = Undefined::UNDEFINED;

    /**
     * @var string
     */
    public $style = Undefined::UNDEFINED;

    /**
     * @var bool
     */
    public $explode = Undefined::UNDEFINED;

    /**
     * @var bool
     */
    public $allowReserved = Undefined::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        JsonContent::class,
        XmlContent::class,
        MediaType::class,
        Property::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Header::class => ['headers', 'header'],
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'contentType' => 'string',
    ];

    public function jsonSerialize(): \stdClass
    {
        $data = parent::jsonSerialize();

        unset($data->property);

        return $data;
    }
}
