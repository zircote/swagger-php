<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * @Annotation
 */
abstract class AbstractOptions extends Operation
{
    /**
     * @inheritdoc
     */
    public $method = 'options';

    /**
     * @inheritdoc
     */
    public static $_parents = [
        PathItem::class,
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class Options extends AbstractOptions
    {
        public function __construct(
            array $properties = [],
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Options extends AbstractOptions
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
