<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Represents a Server.
 *
 * @see [Server Object](https://spec.openapis.org/oas/v3.1.1.html#server-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Server extends AbstractAttribute
{
    /**
     * @param string|null               $url         A URL to the target host
     * @param string|null               $description A description of the host (CommonMark syntax)
     * @param list<ServerVariable>|null $variables   Variables for server URL template substitution
     * @param array<string,mixed>|null  $x           Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $url = null,
        public ?string $description = null,
        public ?array $variables = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function isRoot(): bool
    {
        return true;
    }

    public function merge(): array
    {
        return [PathItem::class => 'servers[]', Operation::class => 'servers[]'];
    }

    public function contains(): array
    {
        return [ServerVariable::class => 'variables[]'];
    }
}
