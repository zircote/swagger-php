<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Discriminator extends \OpenApi\Annotations\Discriminator
{
    public function __construct(
        string $propertyName = Generator::UNDEFINED,
        string $mapping = Generator::UNDEFINED,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'propertyName' => $propertyName,
                'mapping' => $mapping,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
