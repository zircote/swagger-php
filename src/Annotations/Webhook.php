<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * @Annotation
 */
class Webhook extends AbstractAnnotation
{
    /**
     * Key for the webhooks map.
     *
     * @var string
     */
    public $webhook = Generator::UNDEFINED;

    /**
     * The path item.
     *
     * Required unless `reference` is set.
     *
     * @var PathItem
     */
    public $path = Generator::UNDEFINED;

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
        PathItem::class => 'path',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'webhook' => 'string',
    ];

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        if (isset($data->path)) {
            foreach (get_object_vars($data->path) as $property => $value) {
                if ('_' != $property[0] && !Generator::isDefault($value)) {
                    $data->{$property} = $value;
                }
            }
            unset($data->path);
        }

        return $data;
    }
}
