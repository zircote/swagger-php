<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Container for reusable component definitions.
 *
 * Place on a class to declare standalone components that go into the components section
 * of the OpenAPI document. The Components attribute itself is not emitted — its children
 * are promoted to their respective Specification buckets.
 *
 * The primary use case is for DTOs that are NOT roots and therefore cannot be declared
 * at class level on their own: Parameter, Header, Link, and Example. Other types
 * (Schema, PathItem, SecurityScheme, named Response/RequestBody) are already roots and
 * can be declared directly on a class without needing a Components wrapper.
 *
 *   #[Components]
 *   class SharedComponents {
 *       #[Parameter(parameter: 'tenant_id', name: 'tenant_id', in: 'path', schema: new Schema(type: 'string'))]
 *       public string $tenantId;
 *
 *       #[Header(header: 'X-Rate-Limit', schema: new Schema(type: 'integer'))]
 *       public string $rateLimit;
 *
 *       #[Example(example: 'dog', summary: 'A dog', value: ['name' => 'Fido'])]
 *       public string $dogExample;
 *   }
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Components extends AbstractAttribute
{
    public function isRoot(): bool
    {
        return true;
    }

    public function contains(): array
    {
        return [
            Schema::class => 'schemas[]',
            Parameter::class => 'parameters[]',
            Response::class => 'responses[]',
            RequestBody::class => 'requestBodies[]',
            Header::class => 'headers[]',
            Security\Scheme::class => 'securitySchemes[]',
            Link::class => 'links[]',
            Example::class => 'examples[]',
            PathItem::class => 'pathItems[]',
        ];
    }

    /** @var list<Schema> */
    public array $schemas = [];

    /** @var list<Parameter> */
    public array $parameters = [];

    /** @var list<Response> */
    public array $responses = [];

    /** @var list<RequestBody> */
    public array $requestBodies = [];

    /** @var list<Header> */
    public array $headers = [];

    /** @var list<Security\Scheme> */
    public array $securitySchemes = [];

    /** @var list<Link> */
    public array $links = [];

    /** @var list<Example> */
    public array $examples = [];

    /** @var list<PathItem> */
    public array $pathItems = [];
}
