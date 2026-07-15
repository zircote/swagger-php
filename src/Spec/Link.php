<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Describes a possible design-time link for a response.
 *
 * @see [Link Object](https://spec.openapis.org/oas/v3.1.1.html#link-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Link extends AbstractAttribute
{
    /**
     * @param string|null              $link         Reusable link identifier (component key)
     * @param string|null              $operationRef A relative or absolute URI reference to a linked operation
     * @param string|null              $operationId  The name of an existing operation (mutually exclusive with operationRef)
     * @param array<string,mixed>|null $parameters   Values to pass to the linked operation's parameters
     * @param mixed                    $requestBody  A value to use as the request body for the linked operation
     * @param string|null              $description  A description of the link (CommonMark syntax)
     * @param string|null              $ref          A JSON Reference to a reusable link
     * @param Server|null              $server       A server object to be used by the target operation
     * @param array<string,mixed>|null $x            Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables  Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $link = null,
        public ?string $operationRef = null,
        public ?string $operationId = null,
        public ?array $parameters = null,
        public mixed $requestBody = null,
        public ?string $description = null,
        public ?string $ref = null,
        public ?Server $server = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
    }

    public function isRoot(): bool
    {
        return $this->link !== null && $this->ref === null;
    }

    public function merge(): array
    {
        return [Response::class => 'links[]'];
    }
}
