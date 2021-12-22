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
        ?string $name = null,
        ?string $namespace = null,
        ?string $prefix = null,
        ?bool $attribute = null,
        ?bool $wrapped = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'name' => $name ?? Generator::UNDEFINED,
                'namespace' => $namespace ?? Generator::UNDEFINED,
                'prefix' => $prefix ?? Generator::UNDEFINED,
                'attribute' => $attribute ?? Generator::UNDEFINED,
                'wrapped' => $wrapped ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
