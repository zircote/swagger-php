<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute]
class Discriminator extends AbstractAttribute
{
    /**
     * @param array<string,string>|null $mapping
     * @param array<string,mixed>|null  $x
     */
    public function __construct(
        public ?string $propertyName = null,
        public ?array $mapping = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [Schema::class];
    }
}
