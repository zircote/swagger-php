<?php declare(strict_types=1);

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations\Attachable;
use OpenApi\Annotations\Operation;
use OpenApi\Generator;

/**
 * @Annotation
 */
abstract class AbstractCustomAttachable extends Attachable
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

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
    class CustomAttachable extends AbstractCustomAttachable
    {
        public function __construct(
            array $properties = [],
            $value = Generator::UNDEFINED
        ) {
            parent::__construct($properties + [
                'value' => $value,
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class CustomAttachable extends AbstractCustomAttachable
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
