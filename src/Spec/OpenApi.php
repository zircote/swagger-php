<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_CLASS)]
class OpenApi extends AbstractAttribute
{
    /**
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?string $version = null,
        /**
         * @var list<array<string,list<string>>>|null
         */
        public ?array $security = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [];
    }
}
