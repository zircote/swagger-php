<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\CompilerInterface;
use OpenApi\Spec;
use OpenApi\Specification;
use OpenApi\Undefined;

/**
 * Compiles a Specification into an OpenAPI 3.0.x document array.
 *
 * Key differences from 3.1:
 * - JSON Schema draft-04 subset: type is always a string (not array)
 * - nullable is a separate boolean keyword
 * - exclusiveMinimum/Maximum are booleans (used alongside minimum/maximum)
 * - No $ref siblings (summary/description stripped from $ref objects)
 * - No webhooks
 * - No const, no examples array on Schema (only singular example)
 * - No prefixItems, unevaluatedItems, unevaluatedProperties
 * - No if/then/else
 * - No contentMediaType/contentEncoding on Schema
 * - No dependentRequired/dependentSchemas
 * - No propertyNames, contains, minContains, maxContains
 * - License: no identifier field (only url)
 */
class OpenApi30Compiler implements CompilerInterface
{
    protected const VERSIONS = ['3.0.0', '3.0.1', '3.0.2', '3.0.3', '3.0.4'];

    public function getVersion(): string
    {
        return '3.0.0';
    }

    public function supports(string $version): bool
    {
        return in_array($version, static::VERSIONS, true);
    }

    public function validate(Specification $specification): array
    {
        $diagnostics = [];

        if (!$specification->info instanceof Spec\Info) {
            $diagnostics[] = ['level' => 'error', 'message' => 'info is required'];
        } elseif ($specification->info->title === null) {
            $diagnostics[] = ['level' => 'error', 'message' => 'info.title is required'];
        }

        $hasPaths = (bool) array_filter($specification->operations, fn (Spec\Operation $op): bool => $op->path !== null);
        if (!$hasPaths) {
            $diagnostics[] = ['level' => 'warning', 'message' => 'paths is required in OpenAPI 3.0'];
        }

        $hasWebhooks = (bool) array_filter($specification->operations, fn (Spec\Operation $op): bool => $op->webhook !== null);
        if ($hasWebhooks) {
            $diagnostics[] = ['level' => 'warning', 'message' => 'webhooks are not supported in OpenAPI 3.0 and will be omitted'];
        }

        if ($specification->info?->license instanceof Spec\License) {
            $license = $specification->info->license;
            if ($license->identifier !== null) {
                $diagnostics[] = ['level' => 'warning', 'message' => 'License identifier is not supported in OpenAPI 3.0, use url instead'];
            }
        }

        $this->validateSchemas($specification, $diagnostics);

        return $diagnostics;
    }

    public function compile(Specification $specification): array
    {
        $output = ['openapi' => $specification->openapi->version ?? '3.0.0'];

        if ($specification->info instanceof Spec\Info) {
            $output['info'] = $this->compileInfo($specification->info);
        }

        if ($specification->servers) {
            $output['servers'] = array_map($this->compileServer(...), $specification->servers);
        }

        $paths = $this->compilePaths($specification->operations);
        if ($paths) {
            $output['paths'] = $paths;
        }

        if ($specification->tags) {
            $output['tags'] = array_map($this->compileTag(...), $specification->tags);
        }

        if ($specification->openapi->security) {
            $output['security'] = $specification->openapi->security;
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

    protected function compileInfo(Spec\Info $info): array
    {
        return $this->filter([
            'title' => $info->title,
            'description' => $info->description,
            'termsOfService' => $info->termsOfService,
            'contact' => $info->contact instanceof Spec\Contact ? $this->compileContact($info->contact) : null,
            'license' => $info->license instanceof Spec\License ? $this->compileLicense($info->license) : null,
            'version' => $info->version,
        ], $info);
    }

    protected function compileContact(Spec\Contact $contact): array
    {
        return $this->filter([
            'name' => $contact->name,
            'url' => $contact->url,
            'email' => $contact->email,
        ], $contact);
    }

    protected function compileLicense(Spec\License $license): array
    {
        return $this->filter([
            'name' => $license->name,
            'url' => $license->url,
        ], $license);
    }

    protected function compileServer(Spec\Server $server): array
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

    protected function compileServerVariable(Spec\ServerVariable $variable): array
    {
        return $this->filter([
            'default' => $variable->default,
            'enum' => $variable->enum,
            'description' => $variable->description,
        ], $variable);
    }

    protected function compileTag(Spec\Tag $tag): array
    {
        return $this->filter([
            'name' => $tag->name,
            'description' => $tag->description,
            'externalDocs' => $tag->externalDocs instanceof Spec\ExternalDocumentation ? $this->compileExternalDocs($tag->externalDocs) : null,
        ], $tag);
    }

    protected function compileExternalDocs(Spec\ExternalDocumentation $docs): array
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

    protected function compileOperation(Spec\Operation $operation): array
    {
        return $this->filter([
            'tags' => $operation->tags,
            'summary' => $operation->summary,
            'description' => $operation->description,
            'externalDocs' => $operation->externalDocs instanceof Spec\ExternalDocumentation ? $this->compileExternalDocs($operation->externalDocs) : null,
            'operationId' => $operation->operationId,
            'parameters' => $operation->parameters ? array_map($this->compileParameter(...), $operation->parameters) : null,
            'requestBody' => $operation->requestBody instanceof Spec\RequestBody ? $this->compileRequestBody($operation->requestBody) : null,
            'responses' => $operation->responses ? $this->compileResponses($operation->responses) : null,
            'callbacks' => $operation->callbacks,
            'deprecated' => $operation->deprecated,
            'security' => $operation->security,
            'servers' => $operation->servers ? array_map($this->compileServer(...), $operation->servers) : null,
        ], $operation);
    }

    protected function compileParameter(Spec\Parameter $parameter): array
    {
        if ($parameter->ref !== null) {
            return ['$ref' => $parameter->ref];
        }

        $result = $this->filter([
            'name' => $parameter->name,
            'in' => $parameter->in,
            'description' => $parameter->description,
            'required' => $parameter->required,
            'deprecated' => $parameter->deprecated,
            'allowEmptyValue' => $parameter->allowEmptyValue,
            'style' => $parameter->style,
            'explode' => $parameter->explode,
            'allowReserved' => $parameter->allowReserved,
            'schema' => $parameter->schema instanceof Spec\Schema ? $this->compileSchema($parameter->schema) : null,
            'content' => $parameter->content !== null ? $this->compileMediaTypes($parameter->content) : null,
        ], $parameter);

        if ($parameter->example !== Undefined::UNDEFINED) {
            $result['example'] = $parameter->example;
        }
        if ($parameter->examples !== null) {
            $result['examples'] = $this->compileExamples($parameter->examples);
        }

        return $result;
    }

    protected function compileRequestBody(Spec\RequestBody $body): array
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

    protected function compileResponse(Spec\Response $response): array
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

    protected function compileHeader(Spec\Header $header): array
    {
        if ($header->ref !== null) {
            return ['$ref' => $header->ref];
        }

        return $this->filter([
            'description' => $header->description,
            'required' => $header->required,
            'deprecated' => $header->deprecated,
            'schema' => $header->schema instanceof Spec\Schema ? $this->compileSchema($header->schema) : null,
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

    protected function compileMediaType(Spec\MediaType $mediaType): array
    {
        $encoding = null;
        if ($mediaType->encoding) {
            $encoding = [];
            foreach ($mediaType->encoding as $name => $enc) {
                $encoding[$name] = $this->compileEncoding($enc);
            }
        }

        $result = $this->filter([
            'schema' => $mediaType->schema instanceof Spec\Schema ? $this->compileSchema($mediaType->schema) : null,
            'examples' => $mediaType->examples !== null ? $this->compileExamples($mediaType->examples) : null,
            'encoding' => $encoding,
        ], $mediaType);

        if ($mediaType->example !== Undefined::UNDEFINED) {
            $result['example'] = $mediaType->example;
        }

        return $result;
    }

    protected function compileEncoding(Spec\Encoding $encoding): array
    {
        return $this->filter([
            'contentType' => $encoding->contentType,
            'style' => $encoding->style,
            'explode' => $encoding->explode,
            'allowReserved' => $encoding->allowReserved,
        ], $encoding);
    }

    protected function compileLink(Spec\Link $link): array
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
            'server' => $link->server instanceof Spec\Server ? $this->compileServer($link->server) : null,
        ], $link);
    }

    /**
     * Compile schema using OAS 3.0 / JSON Schema draft-04 semantics.
     *
     * - type is always a string
     * - nullable is a separate keyword
     * - exclusiveMinimum/Maximum are booleans
     * - No $ref siblings
     * - No const, prefixItems, unevaluatedItems, unevaluatedProperties,
     *   if/then/else, contentMediaType, contentEncoding, dependentRequired,
     *   dependentSchemas, propertyNames, contains, minContains, maxContains
     */
    protected function compileSchema(Spec\Schema|string $schema): array
    {
        if (is_string($schema)) {
            return ['$ref' => $schema];
        }

        if ($schema->ref !== null) {
            return ['$ref' => $schema->ref];
        }

        $type = $schema->type;
        if (is_array($type)) {
            $type = array_filter($type, fn (string $t): bool => $t !== 'null');
            $type = count($type) === 1 ? reset($type) : ($type[0] ?? null);
        }

        $nullable = $schema->nullable;
        if ($nullable === null && $schema->type !== null) {
            $typeArray = (array) $schema->type;
            if (in_array('null', $typeArray, true)) {
                $nullable = true;
            }
        }

        $exclusiveMinimum = null;
        $exclusiveMaximum = null;
        $minimum = $schema->minimum;
        $maximum = $schema->maximum;

        if ($schema->exclusiveMinimum !== null) {
            if (is_bool($schema->exclusiveMinimum)) {
                $exclusiveMinimum = $schema->exclusiveMinimum ?: null;
            } else {
                $minimum = $schema->exclusiveMinimum;
                $exclusiveMinimum = true;
            }
        }

        if ($schema->exclusiveMaximum !== null) {
            if (is_bool($schema->exclusiveMaximum)) {
                $exclusiveMaximum = $schema->exclusiveMaximum ?: null;
            } else {
                $maximum = $schema->exclusiveMaximum;
                $exclusiveMaximum = true;
            }
        }

        $result = $this->filter([
            'type' => $type,
            'format' => $schema->format,
            'title' => $schema->title,
            'description' => $schema->description,
            'nullable' => $nullable,
            'enum' => $schema->enum,

            // String
            'minLength' => $schema->minLength,
            'maxLength' => $schema->maxLength,
            'pattern' => $schema->pattern,

            // Numeric
            'minimum' => $minimum,
            'maximum' => $maximum,
            'exclusiveMinimum' => $exclusiveMinimum,
            'exclusiveMaximum' => $exclusiveMaximum,
            'multipleOf' => $schema->multipleOf,

            // Array
            'items' => $schema->items !== null ? $this->compileSchema($schema->items) : null,
            'minItems' => $schema->minItems,
            'maxItems' => $schema->maxItems,
            'uniqueItems' => $schema->uniqueItems,

            // Object
            'properties' => $schema->properties !== null ? $this->compileProperties($schema->properties) : null,
            'required' => $schema->required,
            'additionalProperties' => $schema->additionalProperties !== null ? (is_bool($schema->additionalProperties) ? $schema->additionalProperties : $this->compileSchema($schema->additionalProperties)) : null,
            'minProperties' => $schema->minProperties,
            'maxProperties' => $schema->maxProperties,

            // Composition
            'allOf' => $schema->allOf !== null ? array_map($this->compileSchema(...), $schema->allOf) : null,
            'anyOf' => $schema->anyOf !== null ? array_map($this->compileSchema(...), $schema->anyOf) : null,
            'oneOf' => $schema->oneOf !== null ? array_map($this->compileSchema(...), $schema->oneOf) : null,
            'not' => $schema->not instanceof Spec\Schema ? $this->compileSchema($schema->not) : null,

            // Meta
            'deprecated' => $schema->deprecated,
            'readOnly' => $schema->readOnly,
            'writeOnly' => $schema->writeOnly,

            // OpenAPI extensions on schema
            'discriminator' => $schema->discriminator instanceof Spec\Discriminator ? $this->compileDiscriminator($schema->discriminator) : null,
            'externalDocs' => $schema->externalDocs instanceof Spec\ExternalDocumentation ? $this->compileExternalDocs($schema->externalDocs) : null,
            'xml' => $schema->xml instanceof Spec\Xml ? $this->compileXml($schema->xml) : null,
        ], $schema);

        if ($schema->default !== Undefined::UNDEFINED) {
            $result['default'] = $schema->default;
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
            if ($property instanceof Spec\Property) {
                $name = $property->property ?? 'unknown';
                $result[$name] = $property->schema instanceof Spec\Schema
                    ? $this->compileSchema($property->schema)
                    : new \stdClass();
            } elseif ($property instanceof Spec\Schema) {
                $name = $property->schema ?? $property->title ?? 'unknown';
                $result[$name] = $this->compileSchema($property);
            }
        }

        return $result;
    }

    protected function compileDiscriminator(Spec\Discriminator $discriminator): array
    {
        return $this->filter([
            'propertyName' => $discriminator->propertyName,
            'mapping' => $discriminator->mapping,
        ], $discriminator);
    }

    protected function compileXml(Spec\Xml $xml): array
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

    protected function compileSecurityScheme(Spec\SecurityScheme $scheme): array
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

    protected function compileFlow(Spec\Flow $flow): array
    {
        return $this->filter([
            'authorizationUrl' => $flow->authorizationUrl,
            'tokenUrl' => $flow->tokenUrl,
            'refreshUrl' => $flow->refreshUrl,
            'scopes' => $flow->scopes,
        ], $flow);
    }

    protected function compileExample(Spec\Example $example): array
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
            $type = $schema->type;
            if (is_array($type)) {
                $type = array_filter($type, fn (string $t): bool => $t !== 'null');
                $type = reset($type) ?: null;
            }

            if ($type === 'array' && $schema->items === null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"{$schema->schema}\"" : '') . ' has type "array" but no items',
                ];
            }

            if ($schema->prefixItems !== null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"{$schema->schema}\"" : '') . ': prefixItems is not supported in OpenAPI 3.0',
                ];
            }

            if ($schema->unevaluatedProperties !== null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"{$schema->schema}\"" : '') . ': unevaluatedProperties is not supported in OpenAPI 3.0',
                ];
            }

            if ($schema->unevaluatedItems !== null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"{$schema->schema}\"" : '') . ': unevaluatedItems is not supported in OpenAPI 3.0',
                ];
            }

            if ($schema->if instanceof Spec\Schema || $schema->then instanceof Spec\Schema || $schema->else instanceof Spec\Schema) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"{$schema->schema}\"" : '') . ': if/then/else is not supported in OpenAPI 3.0',
                ];
            }

            if ($schema->const !== Undefined::UNDEFINED) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"{$schema->schema}\"" : '') . ': const is not supported in OpenAPI 3.0, using enum fallback',
                ];
            }

            if ($schema->examples !== null) {
                $diagnostics[] = [
                    'level' => 'warning',
                    'message' => 'Schema' . ($schema->schema ? " \"{$schema->schema}\"" : '') . ': examples array is not supported in OpenAPI 3.0, using first value as example',
                ];
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
    protected function walkSchema(Spec\Schema|string $schema, array &$collected): void
    {
        if (is_string($schema)) {
            return;
        }

        $collected[] = $schema;

        if ($schema->properties) {
            foreach ($schema->properties as $prop) {
                if ($prop instanceof Spec\Property && $prop->schema instanceof Spec\Schema) {
                    $this->walkSchema($prop->schema, $collected);
                } elseif ($prop instanceof Spec\Schema) {
                    $this->walkSchema($prop, $collected);
                }
            }
        }
        if ($schema->items instanceof Spec\Schema) {
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
    protected function filter(array $result, Spec\AbstractAttribute $attribute): array
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
