<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Attributes;

use OpenApi\Attributes as OAT;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class CustomAttachable extends OAT\Attachable
{
    /**
     * The attribute value.
     */
    public $value = Generator::UNDEFINED;

    public function __construct($value = Generator::UNDEFINED)
    {
        parent::__construct([
            'value' => $value,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static $_required = ['value'];

    public function allowedParents(): ?array
    {
        return [OAT\Get::class];
    }
}
