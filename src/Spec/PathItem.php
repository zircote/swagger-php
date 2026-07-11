<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Path-level grouping and controller-level metadata.
 *
 * Place on a class whose methods carry Operation attributes. The path is inferred
 * from the operations it contains — no need to declare it explicitly (unlike classic
 * annotations where PathItem carries its own path). Shared parameters, servers,
 * summary and description are emitted at path level in the OpenAPI output.
 *
 * Usage:
 *   #[PathItem(parameters: [new Parameter\Path(name: 'id', ...)])]
 *   class ProductController {
 *       #[Operation\Get(path: '/products/{id}')]
 *       public function get() {}
 *   }
 *
 * The augmenter groups operations by resolved path, then associates this PathItem's
 * spec-level properties (parameters, summary, description, servers) with each path.
 * Controller-level properties (prefix, tags, security, responses) are resolved via
 * class hierarchy and cloned down to operations before compilation.
 *
 * @see [Path Item Object](https://spec.openapis.org/oas/v3.1.1.html#path-item-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class PathItem extends AbstractAttribute
{
    /**
     * @param string|null                     $ref         A JSON Reference to a reusable path item
     * @param string|null                     $prefix      Path prefix — composable via class hierarchy
     * @param string|null                     $summary     An optional summary, intended to apply to all operations in this path
     * @param string|null                     $description An optional description, intended to apply to all operations in this path
     * @param list<Parameter>|null            $parameters  Parameters applicable to all operations under this path
     * @param list<Server>|null               $servers     Alternative servers for all operations under this path
     * @param list<string>|null               $tags        Tags to clone to contained operations
     * @param list<Security\Requirement>|null $security    Security requirements to clone to contained operations
     * @param list<Response>|null             $responses   Shared responses to clone to contained operations
     * @param array<string,mixed>|null        $x           Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $ref = null,
        public ?string $prefix = null,
        public ?string $summary = null,
        public ?string $description = null,
        public ?array $parameters = null,
        public ?array $servers = null,
        public ?array $tags = null,
        public ?array $security = null,
        public ?array $responses = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    /** Resolved path — set by augmenter or HybridBridge, not user-authored. */
    public ?string $path = null;

    public function isRoot(): bool
    {
        return true;
    }

    public function contains(): array
    {
        return [
            Parameter::class => 'parameters[]',
            Server::class => 'servers[]',
            Response::class => 'responses[]',
            Security\Requirement::class => 'security[]',
        ];
    }
}
