<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Represents a Server Variable for server URL template substitution.
 *
 * @see [Server Variable Object](https://spec.openapis.org/oas/v3.1.1.html#server-variable-object)
 */
#[\Attribute(\Attribute::IS_REPEATABLE)]
class ServerVariable extends AbstractAttribute
{
    /**
     * @param string|null              $serverVariable The variable name
     * @param string|null              $default        The default value to use for substitution
     * @param string|null              $description    A description of the server variable (CommonMark syntax)
     * @param list<string>|null        $enum           Enumeration of allowed string values for substitution
     * @param array<string,mixed>|null $x              Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables    Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $serverVariable = null,
        public ?string $default = null,
        public ?string $description = null,
        public ?array $enum = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
    }

    public function merge(): array
    {
        return [Server::class => 'variables[]'];
    }
}
