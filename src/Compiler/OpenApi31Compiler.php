<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\CompilerInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;
use OpenApi\Utils\CollectingLogger;
use Psr\Log\LoggerInterface;

/**
 * Compiles a Specification into an OpenAPI 3.1.x document array.
 */
class OpenApi31Compiler implements CompilerInterface
{
    protected const VERSIONS = ['3.1.0', '3.1.1', '3.1.2'];

    protected CollectingLogger $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = new CollectingLogger($logger);
    }

    public function getVersion(): string
    {
        return '3.1.0';
    }

    public function supports(string $version): bool
    {
        return in_array($version, static::VERSIONS, true);
    }

    public function validate(Specification $specification): array
    {
        if (!$specification->info instanceof OA\Info) {
            $this->logger->error('info is required');
        } elseif ($specification->info->title === null) {
            $this->logger->error('info.title is required');
        }

        $hasPaths = (bool) array_filter($specification->operations, fn (OA\Operation $op): bool => $op->path !== null);
        $hasWebhooks = (bool) array_filter($specification->operations, fn (OA\Operation $op): bool => $op->webhook !== null);
        $hasComponents = $specification->schemas || $specification->responses
            || $specification->parameters || $specification->requestBodies
            || $specification->headers || $specification->securitySchemes
            || $specification->links || $specification->examples;

        if (!$hasPaths && !$hasWebhooks && !$hasComponents) {
            $this->logger->warning('At least one of paths, webhooks, or components is required');
        }

        if ($specification->info?->license instanceof OA\License) {
            $license = $specification->info->license;
            if ($license->url !== null && $license->identifier !== null) {
                $this->logger->warning('License url and identifier are mutually exclusive');
            }
        }

        $this->validateSchemas($specification);

        return $this->logger->entries();
    }

    public function compile(Specification $specification): array
    {
        $version = $specification->openapi->version ?? $this->getVersion();

        return $this->filter([
            'openapi' => $version,
            'info' => $specification->info instanceof OA\Info ? $this->compileInfo($specification->info) : null,
            'servers' => array_map($this->compileServer(...), $specification->servers),
            'paths' => $this->compilePaths($specification->operations, $specification->pathItems),
            'webhooks' => $this->compileWebhooks($specification->operations),
            'tags' => array_map($this->compileTag(...), $specification->tags),
            'security' => $this->compileSecurity($specification->openapi->security ?? []),
            'externalDocs' => $specification->externalDocs ? $this->compileExternalDocs($specification->externalDocs[0]) : null,
            'components' => $this->compileComponents($specification),
        ], $specification->openapi);
    }

    protected function compileInfo(OA\Info $info): array
    {
        return $this->filter([
            'title' => $info->title,
            'description' => $info->description,
            'termsOfService' => $info->termsOfService,
            'contact' => $info->contact instanceof OA\Contact ? $this->compileContact($info->contact) : null,
            'license' => $info->license instanceof OA\License ? $this->compileLicense($info->license) : null,
            'summary' => $info->summary,
            'version' => $info->version,
        ], $info);
    }

    protected function compileContact(OA\Contact $contact): array
    {
        return $this->filter([
            'name' => $contact->name,
            'url' => $contact->url,
            'email' => $contact->email,
        ], $contact);
    }

    protected function compileLicense(OA\License $license): array
    {
        return $this->filter([
            'name' => $license->name,
            'identifier' => $license->identifier,
            'url' => $license->url,
        ], $license);
    }

    protected function compileServer(OA\Server $server): array
    {
        $variables = null;
        if ($server->variables) {
            $variables = [];
            foreach ($server->variables as $variable) {
                if ($variable->serverVariable !== null) {
                    $variables[$variable->serverVariable] = $this->compileServerVariable($variable);
                }
            }
            $variables = $variables ?: null;
        }

        return $this->filter([
            'url' => $server->url,
            'description' => $server->description,
            'variables' => $variables,
        ], $server);
    }

    protected function compileServerVariable(OA\ServerVariable $variable): array
    {
        return $this->filter([
            'default' => $variable->default,
            'enum' => $variable->enum,
            'description' => $variable->description,
        ], $variable);
    }

    protected function compileTag(OA\Tag $tag): array
    {
        return $this->filter([
            'name' => $tag->name,
            'description' => $tag->description,
            'externalDocs' => $tag->externalDocs instanceof OA\ExternalDocumentation ? $this->compileExternalDocs($tag->externalDocs) : null,
        ], $tag);
    }

    protected function compileExternalDocs(OA\ExternalDocumentation $docs): array
    {
        return $this->filter([
            'url' => $docs->url,
            'description' => $docs->description,
        ], $docs);
    }

    /**
     * @param  list<OA\Operation>                $operations
     * @param  list<OA\PathItem>                 $pathItems
     * @return array<string,array<string,mixed>>
     */
    protected function compilePaths(array $operations, array $pathItems = []): array
    {
        $paths = [];

        foreach ($operations as $operation) {
            if ($operation->path === null || $operation->method === null) {
                continue;
            }

            $paths[$operation->path] ??= [];
            $paths[$operation->path][$operation->method] = $this->compileOperation($operation);
        }

        foreach ($pathItems as $pathItem) {
            if ($pathItem->path === null) {
                continue;
            }

            $paths[$pathItem->path] = ($paths[$pathItem->path] ?? []) + $this->compilePathItem($pathItem);
        }

        return $paths;
    }

    protected function compilePathItem(OA\PathItem $pathItem): array
    {
        return $this->filter([
            'summary' => $pathItem->summary,
            'description' => $pathItem->description,
            'parameters' => array_map($this->compileParameter(...), $pathItem->parameters ?? []),
            'servers' => array_map($this->compileServer(...), $pathItem->servers ?? []),
        ], $pathItem);
    }

    /**
     * @param  list<OA\Operation>                $operations
     * @return array<string,array<string,mixed>>
     */
    protected function compileWebhooks(array $operations): array
    {
        $webhooks = [];

        foreach ($operations as $operation) {
            if ($operation->webhook === null || $operation->method === null) {
                continue;
            }

            $webhooks[$operation->webhook] ??= [];
            $webhooks[$operation->webhook][$operation->method] = $this->compileOperation($operation);
        }

        return $webhooks;
    }

    protected function compileOperation(OA\Operation $operation): array
    {
        return $this->filter([
            'tags' => $operation->tags,
            'summary' => $operation->summary,
            'description' => $operation->description,
            'externalDocs' => $operation->externalDocs instanceof OA\ExternalDocumentation ? $this->compileExternalDocs($operation->externalDocs) : null,
            'operationId' => $operation->operationId,
            'parameters' => array_map($this->compileParameter(...), $operation->parameters ?? []),
            'requestBody' => $operation->requestBody instanceof OA\RequestBody ? $this->compileRequestBody($operation->requestBody) : null,
            'responses' => $this->compileResponses($operation->responses ?? []),
            'callbacks' => $this->compileCallbacks($operation->callbacks ?? []),
            'deprecated' => $operation->deprecated,
            'security' => $this->compileSecurity($operation->security ?? []),
            'servers' => array_map($this->compileServer(...), $operation->servers ?? []),
        ], $operation);
    }

    /**
     * Recursively compile callback structures, resolving any DTO objects found within.
     */
    protected function compileCallbacks(array $callbacks): array
    {
        return array_map($this->compileCallbackValue(...), $callbacks);
    }

    protected function compileCallbackValue(mixed $value): mixed
    {
        if ($value instanceof OA\Operation) {
            return $this->compileOperation($value);
        }
        if ($value instanceof OA\RequestBody) {
            return $this->compileRequestBody($value);
        }
        if ($value instanceof OA\Response) {
            return $this->compileResponse($value);
        }
        if ($value instanceof OA\Schema) {
            return $this->compileSchema($value);
        }
        if (is_array($value)) {
            return array_map($this->compileCallbackValue(...), $value);
        }

        return $value;
    }

    protected function compileParameter(OA\Parameter $parameter): array
    {
        if ($parameter->ref !== null) {
            return ['$ref' => $parameter->ref];
        }

        return $this->filter([
            'name' => $parameter->name,
            'in' => $parameter->in,
            'description' => $parameter->description,
            'required' => $parameter->required,
            'deprecated' => $parameter->deprecated,
            'allowEmptyValue' => $parameter->allowEmptyValue,
            'style' => $parameter->style,
            'explode' => $parameter->explode,
            'allowReserved' => $parameter->allowReserved,
            'schema' => $parameter->schema instanceof OA\Schema ? $this->compileSchema($parameter->schema) : null,
            'example' => $parameter->example,
            'examples' => $this->compileExamples($parameter->examples ?? []),
            'content' => $this->compileMediaTypes($parameter->content ?? []),
        ], $parameter);
    }

    protected function compileRequestBody(OA\RequestBody $body): array
    {
        if ($body->ref !== null) {
            return ['$ref' => $body->ref];
        }

        return $this->filter([
            'description' => $body->description,
            'content' => $this->compileMediaTypes($body->content ?? []),
            'required' => $body->required,
        ], $body);
    }

    /**
     * @param  list<OA\Response>   $responses
     * @return array<string,mixed>
     */
    protected function compileResponses(array $responses): array
    {
        return $this->compileNamedMap($responses, fn (OA\Response $response): string => (string) $response->response, $this->compileResponse(...));
    }

    protected function compileResponse(OA\Response $response): array
    {
        if ($response->ref !== null) {
            return ['$ref' => $response->ref];
        }

        return $this->filter([
            'description' => $response->description,
            'headers' => $this->compileNamedMap($response->headers ?? [], 'header', $this->compileHeader(...)),
            'content' => $this->compileMediaTypes($response->content ?? []),
            'links' => $this->compileNamedMap($response->links ?? [], fn (OA\Link $link): string => $link->link ?? $link->operationId ?? 'link', $this->compileLink(...)),
        ], $response);
    }

    protected function compileHeader(OA\Header $header): array
    {
        if ($header->ref !== null) {
            return ['$ref' => $header->ref];
        }

        return $this->filter([
            'description' => $header->description,
            'required' => $header->required,
            'deprecated' => $header->deprecated,
            'style' => $header->style,
            'explode' => $header->explode,
            'schema' => $header->schema instanceof OA\Schema ? $this->compileSchema($header->schema) : null,
            'example' => $header->example,
            'examples' => $this->compileExamples($header->examples ?? []),
            'content' => $this->compileMediaTypes($header->content ?? []),
        ], $header);
    }

    /**
     * @param  list<OA\MediaType>  $mediaTypes
     * @return array<string,mixed>
     */
    protected function compileMediaTypes(array $mediaTypes): array
    {
        return $this->compileNamedMap($mediaTypes, fn (OA\MediaType $mediaType): string => $mediaType->mediaType ?? 'application/json', $this->compileMediaType(...));
    }

    protected function compileMediaType(OA\MediaType $mediaType): array
    {
        return $this->filter([
            'schema' => $mediaType->schema instanceof OA\Schema ? $this->compileSchema($mediaType->schema) : null,
            'example' => $mediaType->example,
            'examples' => $this->compileExamples($mediaType->examples ?? []),
            'encoding' => $this->compileNamedMap($mediaType->encoding ?? [], 'encoding', $this->compileEncoding(...)),
        ], $mediaType);
    }

    protected function compileEncoding(OA\Encoding $encoding): array
    {
        return $this->filter([
            'contentType' => $encoding->contentType,
            'headers' => $this->compileNamedMap($encoding->headers ?? [], 'header', $this->compileHeader(...)),
            'style' => $encoding->style,
            'explode' => $encoding->explode,
            'allowReserved' => $encoding->allowReserved,
        ], $encoding);
    }

    protected function compileLink(OA\Link $link): array
    {
        if ($link->ref !== null) {
            return ['$ref' => $link->ref];
        }

        return $this->filter([
            'operationRef' => $link->operationRef,
            'operationId' => $link->operationId,
            'parameters' => $link->parameters,
            'requestBody' => $link->requestBody,
            'description' => $link->description,
            'server' => $link->server instanceof OA\Server ? $this->compileServer($link->server) : null,
        ], $link);
    }

    protected function compileSchema(OA\Schema|string $schema): array|\stdClass
    {
        if (is_string($schema)) {
            return ['$ref' => $schema];
        }

        if ($schema->ref !== null) {
            if ($schema->nullable === true) {
                return $this->filter([
                    'oneOf' => [
                        $this->filter([
                            '$ref' => $schema->ref,
                            'description' => Undefined::isDefault($schema->description) ? null : $schema->description,
                        ]),
                        ['type' => 'null'],
                    ],
                ], $schema);
            }

            return $this->filter([
                '$ref' => $schema->ref,
                'description' => $schema->description,
            ], $schema);
        }

        $type = $schema->type;
        if ($schema->nullable === true && $type !== null) {
            $type = (array) $type;
            if (!in_array('null', $type, true)) {
                $type[] = 'null';
            }
        }
        if (is_array($type) && count($type) === 1) {
            $type = reset($type);
        }

        $result = $this->filter([
            'type' => $type,
            'format' => $schema->format,
            'title' => $schema->title,
            'description' => $schema->description,
            'enum' => $schema->enum,

            // String
            'minLength' => $schema->minLength,
            'maxLength' => $schema->maxLength,
            'pattern' => $schema->pattern,
            'contentMediaType' => $schema->contentMediaType,
            'contentEncoding' => $schema->contentEncoding,

            // Numeric
            'minimum' => $this->compileMinimum($schema),
            'maximum' => $this->compileMaximum($schema),
            'exclusiveMinimum' => $this->compileExclusiveMinimum($schema),
            'exclusiveMaximum' => $this->compileExclusiveMaximum($schema),
            'multipleOf' => $schema->multipleOf,

            // Array
            'items' => $schema->items !== null ? $this->compileSchema($schema->items) : null,
            'minItems' => $schema->minItems,
            'maxItems' => $schema->maxItems,
            'uniqueItems' => $schema->uniqueItems,
            'prefixItems' => $schema->prefixItems !== null ? array_map($this->compileSchema(...), $schema->prefixItems) : null,
            'contains' => $schema->contains !== null ? (is_bool($schema->contains) ? $schema->contains : $this->compileSchema($schema->contains)) : null,
            'minContains' => $schema->minContains,
            'maxContains' => $schema->maxContains,
            'unevaluatedItems' => $schema->unevaluatedItems !== null ? (is_bool($schema->unevaluatedItems) ? $schema->unevaluatedItems : $this->compileSchema($schema->unevaluatedItems)) : null,

            // Object
            'properties' => $schema->properties !== null ? $this->compileProperties($schema->properties) : null,
            'required' => $schema->required,
            'additionalProperties' => $schema->additionalProperties !== null ? (is_bool($schema->additionalProperties) ? $schema->additionalProperties : $this->compileSchema($schema->additionalProperties)) : null,
            'patternProperties' => $schema->patternProperties !== null ? array_map($this->compileSchema(...), $schema->patternProperties) : null,
            'minProperties' => $schema->minProperties,
            'maxProperties' => $schema->maxProperties,
            'unevaluatedProperties' => $schema->unevaluatedProperties !== null ? (is_bool($schema->unevaluatedProperties) ? $schema->unevaluatedProperties : $this->compileSchema($schema->unevaluatedProperties)) : null,
            'propertyNames' => $schema->propertyNames instanceof OA\Schema ? $this->compileSchema($schema->propertyNames) : null,
            'dependentRequired' => $schema->dependentRequired,
            'dependentSchemas' => $schema->dependentSchemas !== null ? array_map($this->compileSchema(...), $schema->dependentSchemas) : null,

            // Composition
            'allOf' => $schema->allOf !== null ? array_map($this->compileSchema(...), $schema->allOf) : null,
            'anyOf' => $schema->anyOf !== null ? array_map($this->compileSchema(...), $schema->anyOf) : null,
            'oneOf' => $schema->oneOf !== null ? array_map($this->compileSchema(...), $schema->oneOf) : null,
            'not' => $schema->not instanceof OA\Schema ? $this->compileSchema($schema->not) : null,

            // Conditional
            'if' => $schema->if instanceof OA\Schema ? $this->compileSchema($schema->if) : null,
            'then' => $schema->then instanceof OA\Schema ? $this->compileSchema($schema->then) : null,
            'else' => $schema->else instanceof OA\Schema ? $this->compileSchema($schema->else) : null,

            // Examples
            'examples' => $this->compileExamples($schema->examples ?? []),

            // Meta
            'deprecated' => $schema->deprecated,
            'readOnly' => $schema->readOnly,
            'writeOnly' => $schema->writeOnly,

            // OpenAPI extensions on schema
            'discriminator' => $schema->discriminator instanceof OA\Discriminator ? $this->compileDiscriminator($schema->discriminator) : null,
            'externalDocs' => $schema->externalDocs instanceof OA\ExternalDocumentation ? $this->compileExternalDocs($schema->externalDocs) : null,
            'xml' => $schema->xml instanceof OA\Xml ? $this->compileXml($schema->xml) : null,
        ], $schema);

        if ($schema->default !== Undefined::UNDEFINED) {
            $result['default'] = $schema->default;
        }
        if ($schema->const !== Undefined::UNDEFINED) {
            $result['const'] = $schema->const;
        }
        if ($schema->example !== Undefined::UNDEFINED) {
            $result['example'] = $schema->example;
        }

        return $result ?: new \stdClass();
    }

    /**
     * @param  list<OA\Property|OA\Schema> $properties
     * @return array<string,mixed>
     */
    protected function compileProperties(array $properties): array
    {
        $result = [];

        foreach ($properties as $property) {
            if ($property instanceof OA\Property) {
                $name = $property->property ?? 'unknown';
                $result[$name] = $property->schema instanceof OA\Schema
                    ? $this->compileSchema($property->schema)
                    : new \stdClass();
            } elseif ($property instanceof OA\Schema) {
                $name = $property->schema ?? $property->title ?? 'unknown';
                $result[$name] = $this->compileSchema($property);
            }
        }

        return $result;
    }

    protected function compileDiscriminator(OA\Discriminator $discriminator): array
    {
        return $this->filter([
            'propertyName' => $discriminator->propertyName,
            'mapping' => $discriminator->mapping,
        ], $discriminator);
    }

    protected function compileXml(OA\Xml $xml): array
    {
        return $this->filter([
            'name' => $xml->name,
            'namespace' => $xml->namespace,
            'prefix' => $xml->prefix,
            'attribute' => $xml->attribute,
            'wrapped' => $xml->wrapped,
        ], $xml);
    }

    protected function compileComponents(Specification $specification): array
    {
        return array_filter([
            'schemas' => $this->compileNamedMap($specification->schemas, fn (OA\Schema $schema): string => $schema->schema ?? $schema->title ?? 'Schema', $this->compileSchema(...)),
            'responses' => $this->compileNamedMap($specification->responses, fn (OA\Response $response): string => (string) $response->response, $this->compileResponse(...)),
            'parameters' => $this->compileNamedMap($specification->parameters, fn (OA\Parameter $parameter): string => $parameter->parameter ?? $parameter->name ?? 'param', $this->compileParameter(...)),
            'requestBodies' => $this->compileNamedMap($specification->requestBodies, fn (OA\RequestBody $body, int $index): string => $body->request ?? 'body' . $index, $this->compileRequestBody(...)),
            'headers' => $this->compileNamedMap($specification->headers, 'header', $this->compileHeader(...)),
            'securitySchemes' => $this->compileNamedMap($specification->securitySchemes, 'securityScheme', $this->compileSecurityScheme(...)),
            'links' => $this->compileNamedMap($specification->links, fn (OA\Link $link): string => $link->link ?? $link->operationId ?? 'link', $this->compileLink(...)),
            'examples' => $this->compileNamedMap($specification->examples, 'example', $this->compileExample(...)),
        ]);
    }

    /**
     * Passing raw arrays is deprecated; use OA\Security\Requirement instances instead.
     *
     * @param list<OA\Security\Requirement|array<string,list<string>>> $security
     */
    protected function compileSecurity(array $security): array
    {
        return array_map(static function (OA\Security\Requirement|array $item): array {
            if ($item instanceof OA\Security\Requirement) {
                return $item->toArray();
            }

            return $item;
        }, $security);
    }

    protected function compileSecurityScheme(OA\Security\Scheme $scheme): array
    {
        return $this->filter([
            'type' => $scheme->type,
            'description' => $scheme->description,
            'name' => $scheme->name,
            'in' => $scheme->in,
            'scheme' => $scheme->scheme,
            'bearerFormat' => $scheme->bearerFormat,
            'openIdConnectUrl' => $scheme->openIdConnectUrl,
            'flows' => $this->compileFlows($scheme->flows ?? []),
        ], $scheme);
    }

    /**
     * @param list<OA\Flow> $flows
     */
    protected function compileFlows(array $flows): array
    {
        $result = [];

        foreach ($flows as $flow) {
            if ($flow->flow !== null) {
                $result[$flow->flow] = $this->compileFlow($flow);
            }
        }

        return $result;
    }

    protected function compileFlow(OA\Flow $flow): array
    {
        return $this->filter([
            'authorizationUrl' => $flow->authorizationUrl,
            'tokenUrl' => $flow->tokenUrl,
            'refreshUrl' => $flow->refreshUrl,
            'scopes' => $flow->scopes ?? new \stdClass(),
        ], $flow);
    }

    protected function compileExample(OA\Example $example): array
    {
        return $this->filter([
            'summary' => $example->summary,
            'description' => $example->description,
            'value' => $example->value,
            'externalValue' => $example->externalValue,
        ], $example);
    }

    /**
     * @param  list<OA\Example>    $examples
     * @return array<string,mixed>
     */
    protected function compileExamples(array $examples): array
    {
        return $this->compileNamedMap($examples, 'example', $this->compileExample(...));
    }

    protected function validateSchemas(Specification $specification): void
    {
        $allSchemas = $this->collectSchemas($specification);

        foreach ($allSchemas as $schema) {
            if ($schema->type !== null && (is_array($schema->type) ? in_array('array', $schema->type, true) : $schema->type === 'array')) {
                if ($schema->items === null) {
                    $this->logger->warning('Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ' has type "array" but no items');
                }
            }
        }
    }

    /**
     * @return list<OA\Schema>
     */
    protected function collectSchemas(Specification $specification): array
    {
        $schemas = [];
        $specification->getWalker()->visit(OA\Schema::class, function (OA\Schema $schema) use (&$schemas): void {
            $schemas[] = $schema;
        });

        return $schemas;
    }

    /**
     * @param  list<object>        $items
     * @param  string|\Closure     $key   Property name or fn($item, $index): string
     * @return array<string,mixed>
     */
    protected function compileNamedMap(array $items, string|\Closure $key, \Closure $compiler): array
    {
        $result = [];

        foreach ($items as $index => $item) {
            $name = $key instanceof \Closure ? $key($item, $index) : ($item->$key ?? (string) $index);
            $result[$name] = $compiler($item);
        }

        return $result;
    }

    protected function compileMinimum(OA\Schema $schema): int|float|null
    {
        if ($schema->exclusiveMinimum === true) {
            return null;
        }

        return $schema->minimum;
    }

    protected function compileMaximum(OA\Schema $schema): int|float|null
    {
        if ($schema->exclusiveMaximum === true) {
            return null;
        }

        return $schema->maximum;
    }

    protected function compileExclusiveMinimum(OA\Schema $schema): int|float|null
    {
        if ($schema->exclusiveMinimum === true) {
            return $schema->minimum;
        }

        if (is_numeric($schema->exclusiveMinimum)) {
            return $schema->exclusiveMinimum;
        }

        return null;
    }

    protected function compileExclusiveMaximum(OA\Schema $schema): int|float|null
    {
        if ($schema->exclusiveMaximum === true) {
            return $schema->maximum;
        }

        if (is_numeric($schema->exclusiveMaximum)) {
            return $schema->exclusiveMaximum;
        }

        return null;
    }

    /**
     * Remove null entries and apply x- extensions.
     */
    protected function filter(array $result, OA\AbstractAttribute|null $attribute = null): array
    {
        $result = array_filter($result, fn ($value): bool => !in_array($value, [null, Undefined::UNDEFINED, []], true));

        if ($attribute?->x !== null) {
            foreach ($attribute->x as $key => $value) {
                $result['x-' . $key] = $value;
            }
        }

        return $result;
    }
}
