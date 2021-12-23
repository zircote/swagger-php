<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\Attributes;

use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\Get;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class CustomAttachable extends Attachable
{
    /**
     * The attribute value.
     *
     * @var mixed
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
        return [Get::class];
    }
}
