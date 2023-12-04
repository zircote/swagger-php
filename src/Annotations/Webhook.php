<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

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
     * The reference.
     *
     * Required unless `path` is set.
     *
     * @var Reference
     */
    public $reference = Generator::UNDEFINED;

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
        Reference::class => 'reference',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'webhook' => 'string',
    ];
}
