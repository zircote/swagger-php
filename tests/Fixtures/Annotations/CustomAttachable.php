<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\Annotations;

use OpenApi\Annotations\Attachable;
use OpenApi\Annotations\Operation;
use OpenApi\Generator;

/**
 * @Annotation
 */
class CustomAttachable extends Attachable
{
    /**
     * The attribute value.
     *
     * @var mixed
     */
    public $value = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['value'];

    public function allowedParents(): ?array
    {
        return [Operation::class];
    }
}
