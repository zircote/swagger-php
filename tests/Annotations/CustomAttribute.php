<?php declare(strict_types=1);

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations\Attribute;
use OpenApi\Generator;

/**
 * @Annotation
 */
class CustomAttribute extends Attribute
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
}
