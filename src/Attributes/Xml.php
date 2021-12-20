<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Xml extends \OpenApi\Annotations\Xml
{
    public function __construct(
        string $name = Generator::UNDEFINED,
        string $namespace = Generator::UNDEFINED,
        string $prefix = Generator::UNDEFINED,
        ?bool $attribute = null,
        ?bool $wrapped = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'name' => $name,
                'namespace' => $namespace,
                'prefix' => $prefix,
                'attribute' => $attribute ?? Generator::UNDEFINED,
                'wrapped' => $wrapped ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
