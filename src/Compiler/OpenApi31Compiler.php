<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\Contracts\CompilerInterface;
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

    protected const OPERATION_REQUEST_BODY_METHODS = ['put', 'post', 'delete', 'patch'];

    /**
     * Every type the wire format accepts as input. `null` is included for 3.0 as well, where
     * the compiler translates it to `nullable` rather than dropping it.
     */
    protected const SCHEMA_TYPES = ['string', 'number', 'integer', 'boolean', 'array', 'object', 'null'];

    protected const RESPONSE_KEY = '/^(default|[1-5][0-9]{2}|[1-5]XX)$/';

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
            $this->logger->error('info.title is required in ' . $specification->info->getSourceLocation());
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
                $this->logger->warning('License url and identifier are mutually exclusive in ' . $license->getSourceLocation());
            }
        }

        $this->validateSchemas($specification);

        $this->validateOperations($specification);

        $this->validateOperationIds($specification);

        $this->validateResponses($specification);

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

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    protected function compileContact(OA\Contact $contact): array
    {
        return $this->filter([
            'name' => $contact->name,
            'url' => $contact->url,
            'email' => $contact->email,
        ], $contact);
    }

    /**
     * @return array<string,mixed>
     */
    protected function compileLicense(OA\License $license): array
    {
        return $this->filter([
            'name' => $license->name,
            'identifier' => $license->identifier,
            'url' => $license->url,
        ], $license);
    }

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    protected function compileServerVariable(OA\ServerVariable $variable): array
    {
        return $this->filter([
            'default' => $variable->default,
            'enum' => $variable->enum,
            'description' => $variable->description,
        ], $variable);
    }

    /**
     * @return array<string,mixed>
     */
    protected function compileTag(OA\Tag $tag): array
    {
        return $this->filter([
            'name' => $tag->name,
            'description' => $tag->description,
            'externalDocs' => $tag->externalDocs instanceof OA\ExternalDocumentation ? $this->compileExternalDocs($tag->externalDocs) : null,
        ], $tag);
    }

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    protected function compileOperation(OA\Operation $operation): array
    {
        return $this->filter([
            'tags' => $operation->tags,
            'summary' => $operation->summary,
            'description' => $operation->description,
            'externalDocs' => $operation->externalDocs instanceof OA\ExternalDocumentation
                ? $this->compileExternalDocs($operation->externalDocs)
                : null,
            'operationId' => $operation->operationId,
            'parameters' => array_map($this->compileParameter(...), $operation->parameters ?? []),
            'requestBody' => $operation->requestBody instanceof OA\RequestBody
                ? $this->compileRequestBody($operation->requestBody, $operation->method)
                : null,
            'responses' => $this->compileResponses($operation->responses ?? []),
            'callbacks' => $this->compileCallbacks($operation->callbacks ?? []),
            'deprecated' => $operation->deprecated,
            'security' => $this->compileSecurity($operation->security ?? []),
            'servers' => array_map($this->compileServer(...), $operation->servers ?? []),
        ], $operation);
    }

    /**
     * Recursively compile callback structures, resolving any DTO objects found within.
     *
     * @param  array<string,mixed> $callbacks
     * @return array<string,mixed>
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

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    protected function compileRequestBody(OA\RequestBody $body, ?string $method = null): array|\stdClass|null
    {
        if ($method && !in_array($method, static::OPERATION_REQUEST_BODY_METHODS)) {
            return null;
        }

        if ($body->ref !== null) {
            return ['$ref' => $body->ref];
        }

        $result = $this->filter([
            'description' => $body->description,
            'content' => $this->compileMediaTypes($body->content ?? []),
            'required' => $body->required,
        ], $body);

        return $result ?: new \stdClass();
    }

    /**
     * @param  list<OA\Response>   $responses
     * @return array<string,mixed>
     */
    protected function compileResponses(array $responses): array
    {
        return $this->compileNamedMap($responses, fn (OA\Response $response): string => (string) $response->response, $this->compileResponse(...));
    }

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    protected function compileMediaType(OA\MediaType $mediaType): array
    {
        return $this->filter([
            'schema' => $mediaType->schema instanceof OA\Schema ? $this->compileSchema($mediaType->schema) : null,
            'example' => $mediaType->example,
            'examples' => $this->compileExamples($mediaType->examples ?? []),
            'encoding' => $this->compileNamedMap($mediaType->encoding ?? [], 'encoding', $this->compileEncoding(...)),
        ], $mediaType);
    }

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    protected function compileLink(OA\Link $link): array
    {
        if ($link->ref !== null) {
            return ['$ref' => $link->ref];
        }

        $result = $this->filter([
            'operationRef' => $link->operationRef,
            'operationId' => $link->operationId,
            'parameters' => $link->parameters,
            'description' => $link->description,
            'server' => $link->server instanceof OA\Server ? $this->compileServer($link->server) : null,
        ], $link);

        if ($link->requestBody !== Undefined::UNDEFINED) {
            $result['requestBody'] = $link->requestBody;
        }

        return $result;
    }

    /**
     * @return array<string,mixed>|\stdClass
     */
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
     * @param  list<OA\Property>   $properties
     * @return array<string,mixed>
     */
    protected function compileProperties(array $properties): array
    {
        $result = [];

        foreach ($properties as $property) {
            $result[$property->property] = $property->schema instanceof OA\Schema
                ? $this->compileSchema($property->schema)
                : new \stdClass();
        }

        return $result;
    }

    /**
     * @return array<string,mixed>
     */
    protected function compileDiscriminator(OA\Discriminator $discriminator): array
    {
        return $this->filter([
            'propertyName' => $discriminator->propertyName,
            'mapping' => $discriminator->mapping,
        ], $discriminator);
    }

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    protected function compileComponents(Specification $specification): array
    {
        return array_filter([
            'schemas' => $this->compileNamedMap($specification->schemas, fn (OA\Schema $schema): string => $schema->schema ?? $schema->title ?? 'Schema', $this->compileSchema(...)),
            'responses' => $this->compileNamedMap($specification->responses, fn (OA\Response $response): string => (string) $response->response, $this->compileResponse(...)),
            'parameters' => $this->compileNamedMap($specification->parameters, fn (OA\Parameter $parameter): string => $parameter->parameter ?? $parameter->name ?? 'param', $this->compileParameter(...)),
            'requestBodies' => $this->compileNamedMap($specification->requestBodies, fn (OA\RequestBody $body, int $index): string => $body->request ?? 'body' . $index, $this->compileRequestBody(...)),
            'headers' => $this->compileNamedMap($specification->headers, 'header', $this->compileHeader(...)),
            'securitySchemes' => $this->compileSecuritySchemes($specification->securitySchemes),
            'links' => $this->compileNamedMap($specification->links, fn (OA\Link $link): string => $link->link ?? $link->operationId ?? 'link', $this->compileLink(...)),
            'examples' => $this->compileNamedMap($specification->examples, 'example', $this->compileExample(...)),
        ]);
    }

    /**
     * Passing raw arrays is deprecated; use OA\Security\Requirement instances instead.
     *
     * @param  list<OA\Security\Requirement|array<string,list<string>>> $security
     * @return list<array<string,mixed>>
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

    /**
     * @param  list<OA\Security\Scheme> $schemes
     * @return array<string,mixed>
     */
    protected function compileSecuritySchemes(array $schemes): array
    {
        return $this->compileNamedMap($schemes, 'securityScheme', $this->compileSecurityScheme(...));
    }

    /**
     * @return array<string,mixed>
     */
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
     * @param  list<OA\Flow>       $flows
     * @return array<string,mixed>
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

    /**
     * @return array<string,mixed>
     */
    protected function compileFlow(OA\Flow $flow): array
    {
        return $this->filter([
            'authorizationUrl' => $flow->authorizationUrl,
            'tokenUrl' => $flow->tokenUrl,
            'refreshUrl' => $flow->refreshUrl,
            'scopes' => $flow->scopes ?? new \stdClass(),
        ], $flow);
    }

    /**
     * @return array<string,mixed>
     */
    protected function compileExample(OA\Example $example): array
    {
        $result = $this->filter([
            'summary' => $example->summary,
            'description' => $example->description,
            'externalValue' => $example->externalValue,
        ], $example);

        if ($example->value !== Undefined::UNDEFINED) {
            $result['value'] = $example->value;
        }

        return $result;
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
                    $this->logger->warning('Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . ' has type "array" but no items in ' . $schema->getSourceLocation());
                }
            }

            $this->validateSchemaType($schema);
        }
    }

    protected function validateSchemaType(OA\Schema $schema): void
    {
        if ($schema->type === null) {
            return;
        }

        foreach (is_array($schema->type) ? $schema->type : [$schema->type] as $type) {
            if (in_array($type, static::SCHEMA_TYPES, true)) {
                continue;
            }

            $this->logger->warning('Schema' . ($schema->schema ? " \"$schema->schema\"" : '') . " has unknown type \"$type\", expecting one of " . implode(', ', static::SCHEMA_TYPES) . ' in ' . $schema->getSourceLocation());
        }
    }

    protected function validateOperations(Specification $specification): void
    {
        $specification->getWalker()->visit(OA\Operation::class, function (OA\Operation $operation): void {
            if ($operation->requestBody instanceof OA\RequestBody && !in_array($operation->method, static::OPERATION_REQUEST_BODY_METHODS)) {
                $this->logger->warning("Request body not supported for method {$operation->method} in " . $operation->getSourceLocation());
            }
        });
    }

    /**
     * Generated ids cannot collide — `OperationIds` derives them from method, path and
     * source — so this only ever fires for values set by hand.
     *
     * Uniqueness is a property of the document, so an operation carrying neither a path nor
     * a webhook is skipped: it is emitted nowhere and its id is never written down.
     */
    protected function validateOperationIds(Specification $specification): void
    {
        $seen = [];
        $specification->getWalker()->visit(OA\Operation::class, function (OA\Operation $operation) use (&$seen): void {
            if ($operation->operationId === null || ($operation->path === null && $operation->webhook === null)) {
                return;
            }

            if (isset($seen[$operation->operationId])) {
                $this->logger->warning("operationId must be unique, found \"{$operation->operationId}\" again in " . $operation->getSourceLocation());

                return;
            }

            $seen[$operation->operationId] = true;
        });
    }

    /**
     * Only responses nested in an operation are checked, because position is what gives
     * `Response::$response` its meaning: a status code there, and a component key in the
     * `responses` bucket, where `components.responses.product` is a name. `isRoot()` cannot
     * tell them apart — it is true whenever the key is set.
     *
     * A nested response carries no reflector of its own, so the operation is reported
     * instead, which is where the reader has to go to fix it anyway.
     */
    protected function validateResponses(Specification $specification): void
    {
        $specification->getWalker()->visit(OA\Operation::class, function (OA\Operation $operation): void {
            foreach ($operation->responses ?? [] as $response) {
                if ($response->response === null) {
                    continue;
                }

                if (preg_match(static::RESPONSE_KEY, (string) $response->response) !== 1) {
                    $this->logger->warning("Invalid response \"{$response->response}\", expecting \"default\", a HTTP status code or a range such as \"2XX\" in " . $operation->getSourceLocation());
                }
            }
        });
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
     *
     * @param  array<string,mixed> $result
     * @return array<string,mixed>
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
