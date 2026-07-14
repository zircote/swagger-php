<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Describes shared metadata for all operations under a path.
 *
 * Place on a controller class — the path is inferred from its operations.
 * Parameters, summary, description and servers are emitted at path level in the
 * OpenAPI output. Prefix, tags, security and responses are controller-level features
 * that compose via class hierarchy and apply to all contained operations.
 *
 * Shared path-level properties (parameters, summary, description, servers per OpenAPI spec):
 *
 *   #[PathItem(parameters: [new Parameter\Path(name: 'id', schema: new Schema(type: 'integer'))])]
 *   class ProductController {
 *       #[Operation\Get(path: '/products/{id}')]
 *       public function get() {}
 *   }
 *
 * The path in the output is inferred from the operations — no need to declare it
 * on PathItem, avoiding duplication.
 *
 * Prefix composition with inherited metadata:
 *
 *   #[PathItem(prefix: '/api/v1')]
 *   class BaseController {}
 *
 *   #[PathItem(prefix: '/users', tags: ['Users'], security: [new Security\Requirement(scheme: 'bearerAuth')])]
 *   #[Response(response: 401, description: 'Unauthorized')]
 *   #[Response(response: 500, description: 'Server error')]
 *   class UserController extends BaseController {
 *       #[Operation\Get(path: '/list')]       // resolved: /api/v1/users/list, tags: ['Users']
 *       public function list() {}
 *
 *       #[Operation\Get(path: '/{id}')]       // resolved: /api/v1/users/{id}, tags: ['Users']
 *       public function get() {}
 *   }
 *
 * Prefixes compose by walking the class hierarchy — each ancestor PathItem contributes
 * its prefix segment. All collection properties merge additively: tags, security,
 * responses, and parameters accumulate from the full ancestor chain. Deduplication
 * is by value (tags), by scheme (security), by status code (responses), and by
 * name+in (parameters).
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
     * @param list<Attachable>|null           $attachables Reusable custom attachable attributes
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
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
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
