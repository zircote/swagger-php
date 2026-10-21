<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

/**
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Discriminator` instead
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Discriminator extends OA\Discriminator
{
    /**
     * @param array<string,string>|null $mapping
     * @param array<string,mixed>|null  $x
     * @param list<Attachable>|null     $attachables
     */
    public function __construct(
        ?string $propertyName = null,
        ?array $mapping = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'propertyName' => $propertyName ?? Undefined::UNDEFINED,
            'mapping' => $mapping ?? Undefined::UNDEFINED,
            'x' => $x ?? Undefined::UNDEFINED,
            'attachables' => $attachables ?? Undefined::UNDEFINED,
        ]);
    }
}
