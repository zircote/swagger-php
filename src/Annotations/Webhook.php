<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Acts like a `PathItem` with the main difference being that it requires `webhook` instead of `path`.
 *
 * @Annotation
 */
class Webhook extends PathItem
{
    /**
     * Key for the webhooks map.
     *
     * @var string
     */
    public $webhook = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['webhook'];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        OpenApi::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'webhook' => 'string',
    ];
}
