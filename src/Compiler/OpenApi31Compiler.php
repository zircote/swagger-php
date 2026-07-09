<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\CompilerInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;

/**
 * Compiles a Specification into an OpenAPI 3.1.x document array.
 */
class OpenApi31Compiler implements CompilerInterface
{
    protected const VERSIONS = ['3.1.0', '3.1.1', '3.1.2'];

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
        $diagnostics = [];

        if (!$specification->info instanceof OA\Info) {
            $diagnostics[] = ['level' => 'error', 'message' => 'info is required'];
        } elseif ($specification->info->title === null) {
            $diagnostics[] = ['level' => 'error', 'message' => 'info.title is required'];
        }

        $hasPaths = (bool) array_filter($specification->operations, fn (OA\Operation $op): bool => $op->path !== null);
        $hasWebhooks = (bool) array_filter($specification->operations, fn (OA\Operation $op): bool => $op->webhook !== null);
        $hasComponents = $specification->schemas || $specification->responses
            || $specification->parameters || $specification->requestBodies
            || $specification->headers || $specification->securitySchemes
            || $specification->links || $specification->examples;

        if (!$hasPaths && !$hasWebhooks && !$hasComponents) {
            $diagnostics[] = ['level' => 'warning', 'message' => 'At least one of paths, webhooks, or components is required'];
        }

        if ($specification->info?->license instanceof OA\License) {
            $license = $specification->info->license;
            if ($license->url !== null && $license->identifier !== null) {
                $diagnostics[] = ['level' => 'warning', 'message' => 'License url and identifier are mutually exclusive'];
            }
        }

        $this->validateSchemas($specification, $diagnostics);

        return $diagnostics;
    }

    public function compile(Specification $specification): array
    {
        $output = ['openapi' => $specification->openapi->version ?? '3.1.0'];

        if ($specification->info instanceof OA\Info) {
            $output['info'] = $this->compileInfo($specification->info);
        }

        if ($specification->servers) {
            $output['servers'] = array_map($this->compileServer(...), $specification->servers);
        }

        $paths = $this->compilePaths($specification->operations);
        if ($paths) {
            $output['paths'] = $paths;
        }

        $webhooks = $this->compileWebhooks($specification->operations);
        if ($webhooks) {
            $output['webhooks'] = $webhooks;
        }

        if ($specification->tags) {
            $output['tags'] = array_map($this->compileTag(...), $specification->tags);
        }

        if ($specification->openapi->security) {
            $output['security'] = $this->compileSecurity($specification->openapi->security);
        }

        if ($specification->externalDocs) {
            $output['externalDocs'] = $this->compileExternalDocs($specification->externalDocs[0]);
        }

        $components = $this->compileComponents($specification);
        if ($components) {
            $output['components'] = $components;
        }

        return $output;
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
     * @param  list<Spec\Operation>              $operations
     * @return array<string,array<string,mixed>>
     */
    protected function compilePaths(array $operations): array
    {
        $paths = [];

        foreach ($operations as $operation) {
            if ($operation->path === null || $operation->method === null) {
                continue;
            }

            $paths[$operation->path] ??= [];
            $paths[$operation->path][$operation->method] = $this->compileOperation($operation);
        }

        return $paths;
    }

    /**
     * @param  list<Spec\Operation>              $operations
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
            'parameters' => $operation->parameters ? array_map($this->compileParameter(...), $operation->parameters) : null,
            'requestBody' => $operation->requestBody instanceof OA\RequestBody ? $this->compileRequestBody($operation->requestBody) : null,
            'responses' => $operation->responses ? $this->compileResponses($operation->responses) : null,
            'callbacks' => $operation->callbacks,
            'deprecated' => $operation->deprecated,
            'security' => $operation->security ? $this->compileSecurity($operation->security) : null,
            'servers' => $operation->servers ? array_map($this->compileServer(...), $operation->servers) : null,
        ], $operation);
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
            'examples' => $parameter->examples !== null ? $this->compileExamples($parameter->examples) : null,
            'content' => $parameter->content !== null ? $this->compileMediaTypes($parameter->content) : null,
        ], $parameter);
    }

    protected function compileRequestBody(OA\RequestBody $body): array
    {
        if ($body->ref !== null) {
            return ['$ref' => $body->ref];
        }

        return $this->filter([
            'description' => $body->description,
            'content' => $body->content ? $this->compileMediaTypes($body->content) : null,
            'required' => $body->required,
        ], $body);
    }

    /**
     * @param  list<Spec\Response> $responses
     * @return array<string,mixed>
     */
    protected function compileResponses(array $responses): array
    {
        $result = [];

        foreach ($responses as $response) {
            $key = (string) $response->response;
            $result[$key] = $this->compileResponse($response);
        }

        return $result;
    }

    protected function compileResponse(OA\Response $response): array
    {
        if ($response->ref !== null) {
            return ['$ref' => $response->ref];
        }

        $headers = null;
        if ($response->headers) {
            $headers = [];
            foreach ($response->headers as $header) {
                if ($header->header !== null) {
                    $headers[$header->header] = $this->compileHeader($header);
                }
            }
            $headers = $headers ?: null;
        }

        $links = null;
        if ($response->links) {
            $links = [];
            foreach ($response->links as $link) {
                $name = $link->link ?? $link->operationId ?? 'link';
                $links[$name] = $this->compileLink($link);
            }
        }

        return $this->filter([
            'description' => $response->description,
            'headers' => $headers,
            'content' => $response->content ? $this->compileMediaTypes($response->content) : null,
            'links' => $links,
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
            'schema' => $header->schema instanceof OA\Schema ? $this->compileSchema($header->schema) : null,
        ], $header);
    }

    /**
     * @param  list<Spec\MediaType> $mediaTypes
     * @return array<string,mixed>
     */
    protected function compileMediaTypes(array $mediaTypes): array
    {
        $result = [];

        foreach ($mediaTypes as $mediaType) {
            $key = $mediaType->mediaType ?? 'application/json';
            $result[$key] = $this->compileMediaType($mediaType);
        }

        return $result;
    }

    protected function compileMediaType(OA\MediaType $mediaType): array
    {
        $encoding = null;
        if ($mediaType->encoding) {
            $encoding = [];
            foreach ($mediaType->encoding as $name => $enc) {
                $encoding[$name] = $this->compileEncoding($enc);
            }
        }

        return $this->filter([
            'schema' => $mediaType->schema instanceof OA\Schema ? $this->compileSchema($mediaType->schema) : null,
            'example' => $mediaType->example,
            'examples' => $mediaType->examples !== null ? $this->compileExamples($mediaType->examples) : null,
            'encoding' => $encoding,
        ], $mediaType);
    }

    protected function compileEncoding(OA\Encoding $encoding): array
    {
        return $this->filter([
            'contentType' => $encoding->contentType,
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

    protected function compileSchema(OA\Schema|string $schema): array
    {
        if (is_string($schema)) {
            return ['$ref' => $schema];
        }

        if ($schema->ref !== null) {
            $ref = ['$ref' => $schema->ref];
            if ($schema->description !== null) {
                $ref['description'] = $schema->description;
            }

            return $ref;
        }

        $type = $schema->type;
        if ($schema->nullable === true && $type !== null) {
            $type = (array) $type;
            $type[] = 'null';
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
            'minimum' => $schema->minimum,
            'maximum' => $schema->maximum,
            'exclusiveMinimum' => $schema->exclusiveMinimum,
            'exclusiveMaximum' => $schema->exclusiveMaximum,
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
            'examples' => $schema->examples,

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

        return $result;
    }

    /**
     * @param  list<Spec\Property|Spec\Schema> $properties
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
        $components = [];

        if ($specification->schemas) {
            $schemas = [];
            foreach ($specification->schemas as $schema) {
                $name = $schema->schema ?? $schema->title ?? 'Schema';
                $schemas[$name] = $this->compileSchema($schema);
            }
            $components['schemas'] = $schemas;
        }

        if ($specification->responses) {
            $responses = [];
            foreach ($specification->responses as $response) {
                $key = (string) $response->response;
                $responses[$key] = $this->compileResponse($response);
            }
            $components['responses'] = $responses;
        }

        if ($specification->parameters) {
            $parameters = [];
            foreach ($specification->parameters as $parameter) {
                $name = $parameter->parameter ?? $parameter->name ?? 'param';
                $parameters[$name] = $this->compileParameter($parameter);
            }
            $components['parameters'] = $parameters;
        }

        if ($specification->requestBodies) {
            $bodies = [];
            foreach ($specification->requestBodies as $i => $body) {
                $name = $body->request ?? 'body' . $i;
                $bodies[$name] = $this->compileRequestBody($body);
            }
            $components['requestBodies'] = $bodies;
        }

        if ($specification->headers) {
            $headers = [];
            foreach ($specification->headers as $header) {
                $name = $header->header ?? 'header';
                $headers[$name] = $this->compileHeader($header);
            }
            $components['headers'] = $headers;
        }

        if ($specification->securitySchemes) {
            $schemes = [];
            foreach ($specification->securitySchemes as $scheme) {
                $name = $scheme->securityScheme ?? 'scheme';
                $schemes[$name] = $this->compileSecurityScheme($scheme);
            }
            $components['securitySchemes'] = $schemes;
        }

        if ($specification->links) {
            $links = [];
            foreach ($specification->links as $link) {
                $name = $link->link ?? $link->operationId ?? 'link';
                $links[$name] = $this->compileLink($link);
            }
            $components['links'] = $links;
        }

        if ($specification->examples) {
            $examples = [];
            foreach ($specification->examples as $example) {
                $name = $example->example ?? 'example';
                $examples[$name] = $this->compileExample($example);
            }
            $components['examples'] = $examples;
        }

        return $components;
    }

    /**
     * @param list<Requirement|array<string,list<string>>> $security
     */
    protected function compileSecurity(array $security): array
    {
        return array_map(function (array|OA\Security\Requirement $item): array {
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
            'flows' => $scheme->flows !== null ? $this->compileFlows($scheme->flows) : null,
        ], $scheme);
    }

    /**
     * @param list<Spec\Flow> $flows
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
            'scopes' => $flow->scopes,
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
     * @param  list<Spec\Example>  $examples
     * @return array<string,mixed>
     */
    protected function compileExamples(array $examples): array
    {
        $result = [];

        foreach ($examples as $example) {
            $name = $example->example ?? 'example';
            $result[$name] = $this->compileExample($example);
        }

        return $result;
    }

    /**
     * @param list<array{level: string, message: string}> $diagnostics
     */
    protected function validateSchemas(Specification $specification, array &$diagnostics): void
    {
        $allSchemas = $this->collectSchemas($specification);

        foreach ($allSchemas as $schema) {
            if ($schema->type !== null && (is_array($schema->type) ? in_array('array', $schema->type, true) : $schema->type === 'array')) {
                if ($schema->items === null) {
                    $diagnostics[] = [
                        'level' => 'warning',
                        'message' => 'Schema' . ($schema->schema ? " \"{$schema->schema}\"" : '') . ' has type "array" but no items',
                    ];
                }
            }
        }
    }

    /**
     * @return list<Spec\Schema>
     */
    protected function collectSchemas(Specification $specification): array
    {
        $schemas = [];

        foreach ($specification->schemas as $schema) {
            $this->walkSchema($schema, $schemas);
        }

        foreach ($specification->operations as $operation) {
            if ($operation->parameters) {
                foreach ($operation->parameters as $param) {
                    if ($param->schema !== null) {
                        $this->walkSchema($param->schema, $schemas);
                    }
                }
            }
            if ($operation->requestBody?->content) {
                foreach ($operation->requestBody->content as $mediaType) {
                    if ($mediaType->schema !== null) {
                        $this->walkSchema($mediaType->schema, $schemas);
                    }
                }
            }
            if ($operation->responses) {
                foreach ($operation->responses as $response) {
                    if ($response->content) {
                        foreach ($response->content as $mediaType) {
                            if ($mediaType->schema !== null) {
                                $this->walkSchema($mediaType->schema, $schemas);
                            }
                        }
                    }
                }
            }
        }

        return $schemas;
    }

    /**
     * @param list<Spec\Schema> $collected
     */
    protected function walkSchema(OA\Schema $schema, array &$collected): void
    {
        $collected[] = $schema;

        if ($schema->properties) {
            foreach ($schema->properties as $prop) {
                if ($prop instanceof OA\Property && $prop->schema instanceof OA\Schema) {
                    $this->walkSchema($prop->schema, $collected);
                } elseif ($prop instanceof OA\Schema) {
                    $this->walkSchema($prop, $collected);
                }
            }
        }
        if ($schema->items instanceof OA\Schema) {
            $this->walkSchema($schema->items, $collected);
        }
        if ($schema->allOf) {
            foreach ($schema->allOf as $sub) {
                $this->walkSchema($sub, $collected);
            }
        }
        if ($schema->anyOf) {
            foreach ($schema->anyOf as $sub) {
                $this->walkSchema($sub, $collected);
            }
        }
        if ($schema->oneOf) {
            foreach ($schema->oneOf as $sub) {
                $this->walkSchema($sub, $collected);
            }
        }
    }

    /**
     * Remove null entries and apply x- extensions.
     */
    protected function filter(array $result, OA\AbstractAttribute $attribute): array
    {
        $result = array_filter($result, fn ($value): bool => !in_array($value, [null, Undefined::UNDEFINED, []], true));

        if ($attribute->x !== null) {
            foreach ($attribute->x as $key => $value) {
                $result['x-' . $key] = $value;
            }
        }

        return $result;
    }
}
