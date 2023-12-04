<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

class Reference extends AbstractAnnotation
{
    /**
     * The reference identifier.
     *
     * @var string|class-string|object
     */
    public $ref = Generator::UNDEFINED;

    /**
     * The summary.
     *
     * @var string
     */
    public $summary = Generator::UNDEFINED;

    /**
     * The summary.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Webhook::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'ref' => 'string',
        'summary' => 'string',
        'description' => 'string',
    ];
}
