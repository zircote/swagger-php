<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Contracts\RefInterface;
use OpenApi\Spec\Concerns\RefTrait;

/**
 * Describes the requests an API sends out of band, in response to one of its operations.
 *
 * Nest it in an operation's `callbacks`, keyed by the callback's name, or give it a `component:`
 * name to declare it once and reference it from each operation that sends it:
 *
 *   #[Callback(component: 'productChanged', expression: '{$request.query.callbackUrl}', operations: [
 *       new Operation\Post(requestBody: new RequestBody(...), responses: [new Response(response: 200, description: 'Received')]),
 *   ])]
 *   class ProductEvents {}
 *
 *   #[Operation\Post(path: '/products/subscribe', callbacks: ['onChange' => new Callback(ref: ProductEvents::class)])]
 *
 * A callback covers one expression. A raw array in `callbacks` still takes several.
 *
 * @see [Callback Object](https://spec.openapis.org/oas/v3.1.1.html#callback-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Callback extends AbstractAttribute implements RefInterface
{
    use RefTrait;

    /** The name this attribute is filed under in `components`; null for an inline one. */
    public ?string $component = null;

    /**
     * @param string|null              $expression  The runtime expression for the URL the requests go to, such as `{$request.query.callbackUrl}`
     * @param list<Operation>|null     $operations  The requests sent to that URL, one per method; each names its method, as `Operation\Post` does
     * @param string|null              $ref         A reference to a reusable callback
     * @param string|null              $component   The name this is filed under in `components`, which makes it a reusable component
     * @param array<string,mixed>|null $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables Reusable custom attachable attributes
     * @param Fan|null                 $fan         Where this attribute goes when more than one attribute could hold it
     */
    public function __construct(
        public ?string $expression = null,
        public ?array $operations = null,
        public string|Schema\Ref|null $ref = null,
        ?string $component = null,
        ?array $x = null,
        ?array $attachables = null,
        ?Fan $fan = null,
    ) {
        $this->component = $component;

        parent::__construct(x: $x, attachables: $attachables, fan: $fan);
    }

    public function isRoot(): bool
    {
        return $this->ref === null && $this->component !== null;
    }

    public function merge(): array
    {
        return [
            Components::class => 'callbacks[]',
        ];
    }

    public function contained(): array
    {
        return [
            Components::class => 'callbacks[]',
        ];
    }
}
