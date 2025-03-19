<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Acts like a <code>PathItem</code> with the main difference being that it requires <code>webhook</code> instead of <code>path</code>.
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
