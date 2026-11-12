<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\ParserInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Reads an OpenAPI 3.1.x document into a Specification, the inverse of `OpenApi31Compiler`.
 *
 * The parser reads what the compiler writes. A component lands in its bucket with its name in
 * `component`, an operation is filed by path or webhook and method, and every map keyed by a
 * name becomes a list keyed by the member field the compiler reads it from. `$ref` stays a
 * string. `x-` keys go to `x`. A keyword the model has nowhere to put is left out, with a
 * warning naming where it was: among them `jsonSchemaDialect`, a schema's `$id`, `$comment` and
 * `$defs`, and the schema `false` where the model takes only a `Schema`. So is a value of the
 * wrong type, except a number where a string belongs, which is read as that string.
 */
class OpenApi31Parser implements ParserInterface
{
    /**
     * The versions this parser reads: any patch release of the minor.
     */
    protected const VERSION_PREFIX = '3.1.';

    /**
     * The methods a path item has an operation field for.
     */
    protected const OPERATION_METHODS = ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'];

    protected LoggerInterface $logger;

    /**
     * @var array<class-string, array<string, \ReflectionType|null>>
     */
    protected array $types = [];

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function supports(string $version): bool
    {
        return str_starts_with($version, static::VERSION_PREFIX);
    }

    /**
     * @param array<string, mixed> $document the document as decoded from JSON or YAML, with maps as arrays
     */
    public function parse(array $document): Specification
    {
        $specification = new Specification();
        $specification->add($this->build(OA\OpenApi::class, $document, '', [
            'openapi' => 'version',
            'security' => ['security', $this->parseSecurity(...)],
            'info' => null,
            'servers' => null,
            'paths' => null,
            'webhooks' => null,
            'components' => null,
            'tags' => null,
            'externalDocs' => null,
        ]));

        if (isset($document['info'])) {
            $this->addOne($specification, $document['info'], '/info', $this->parseInfo(...));
        }
        $specification->add(...$this->list($document['servers'] ?? null, '/servers', $this->parseServer(...)));
        foreach ($this->keyedList($document['paths'] ?? null, '/paths', fn (array $item, string $pointer, string $path): array => $this->parsePath($item, $pointer, path: $path)) as $attributes) {
            $specification->add(...$attributes);
        }
        foreach ($this->keyedList($document['webhooks'] ?? null, '/webhooks', fn (array $item, string $pointer, string $name): array => $this->parsePath($item, $pointer, webhook: $name)) as $attributes) {
            $specification->add(...$attributes);
        }
        if (isset($document['components'])) {
            $components = $this->object($document['components'], '/components');
            if ($components !== null) {
                $this->parseComponents($specification, $components, '/components');
            }
        }
        $specification->add(...$this->list($document['tags'] ?? null, '/tags', $this->parseTag(...)));
        if (isset($document['externalDocs'])) {
            $this->addOne($specification, $document['externalDocs'], '/externalDocs', $this->parseExternalDocs(...));
        }

        return $specification;
    }

    /**
     * @param callable(array<string, mixed>, string): AttributeInterface $reader
     */
    protected function addOne(Specification $specification, mixed $value, string $pointer, callable $reader): void
    {
        $attribute = $this->one($reader)($value, $pointer);
        if ($attribute instanceof AttributeInterface) {
            $specification->add($attribute);
        }
    }

    /**
     * @param array<string, mixed> $info
     */
    protected function parseInfo(array $info, string $pointer): OA\Info
    {
        return $this->build(OA\Info::class, $info, $pointer, [
            'title' => 'title',
            'summary' => 'summary',
            'description' => 'description',
            'termsOfService' => 'termsOfService',
            'contact' => ['contact', $this->one($this->parseContact(...))],
            'license' => ['license', $this->one($this->parseLicense(...))],
            'version' => 'version',
        ]);
    }

    /**
     * @param array<string, mixed> $contact
     */
    protected function parseContact(array $contact, string $pointer): OA\Contact
    {
        return $this->build(OA\Contact::class, $contact, $pointer, ['name' => 'name', 'url' => 'url', 'email' => 'email']);
    }

    /**
     * @param array<string, mixed> $license
     */
    protected function parseLicense(array $license, string $pointer): OA\License
    {
        return $this->build(OA\License::class, $license, $pointer, ['name' => 'name', 'identifier' => 'identifier', 'url' => 'url']);
    }

    /**
     * @param array<string, mixed> $server
     */
    protected function parseServer(array $server, string $pointer): OA\Server
    {
        return $this->build(OA\Server::class, $server, $pointer, [
            'url' => 'url',
            'description' => 'description',
            'variables' => ['variables', fn (mixed $variables, string $pointer): array => $this->keyedList($variables, $pointer, $this->parseServerVariable(...))],
        ]);
    }

    /**
     * @param array<string, mixed> $variable
     */
    protected function parseServerVariable(array $variable, string $pointer, string $serverVariable): OA\ServerVariable
    {
        return $this->build(OA\ServerVariable::class, $variable, $pointer, [
            'enum' => 'enum',
            'default' => 'default',
            'description' => 'description',
        ], ['serverVariable' => $serverVariable]);
    }

    /**
     * @param array<string, mixed> $tag
     */
    protected function parseTag(array $tag, string $pointer): OA\Tag
    {
        return $this->build(OA\Tag::class, $tag, $pointer, [
            'name' => 'name',
            'description' => 'description',
            'externalDocs' => ['externalDocs', $this->one($this->parseExternalDocs(...))],
        ]);
    }

    /**
     * @param array<string, mixed> $docs
     */
    protected function parseExternalDocs(array $docs, string $pointer): OA\ExternalDocumentation
    {
        return $this->build(OA\ExternalDocumentation::class, $docs, $pointer, ['url' => 'url', 'description' => 'description']);
    }

    /**
     * One entry of `paths` or `webhooks`: an operation per method, and the path's own fields as a path item.
     *
     * Webhooks compile from operations only, so a webhook's own fields have nowhere to go, and
     * nor has a webhook with no operations. A path with neither is kept as an empty path item.
     *
     * @param array<string, mixed> $item
     *
     * @return list<OA\Operation|OA\PathItem>
     */
    protected function parsePath(array $item, string $pointer, ?string $path = null, ?string $webhook = null): array
    {
        $attributes = [];
        $fields = [];
        foreach ($item as $key => $value) {
            if (in_array($key, static::OPERATION_METHODS, true)) {
                $operationPointer = $this->pointer($pointer, $key);
                $operation = $this->object($value, $operationPointer);
                if ($operation !== null) {
                    $attributes[] = $this->parseOperation($operation, $operationPointer, path: $path, webhook: $webhook, method: $key);
                }
            } else {
                $fields[$key] = $value;
            }
        }

        if ($webhook !== null) {
            if ($attributes === [] && $fields === []) {
                $this->logger->warning(sprintf('A webhook without operations is not supported, left out at %s', $this->location($pointer)));
            }
            $this->unsupported($pointer, array_keys($fields));

            return $attributes;
        }

        if ($fields !== [] || $attributes === []) {
            $pathItem = $this->parsePathItem($fields, $pointer);
            $pathItem->path = $path;
            $attributes[] = $pathItem;
        }

        return $attributes;
    }

    /**
     * @param array<string, mixed> $pathItem
     * @param array<string, mixed> $identity
     */
    protected function parsePathItem(array $pathItem, string $pointer, array $identity = []): OA\PathItem
    {
        return $this->build(OA\PathItem::class, $pathItem, $pointer, [
            '$ref' => 'ref',
            'summary' => 'summary',
            'description' => 'description',
            'parameters' => ['parameters', fn (mixed $parameters, string $pointer): array => $this->list($parameters, $pointer, $this->parseParameter(...))],
            'servers' => ['servers', fn (mixed $servers, string $pointer): array => $this->list($servers, $pointer, $this->parseServer(...))],
        ], $identity);
    }

    /**
     * @param array<string, mixed> $operation
     */
    protected function parseOperation(array $operation, string $pointer, ?string $path, ?string $webhook, string $method): OA\Operation
    {
        return $this->build(OA\Operation::class, $operation, $pointer, [
            'tags' => 'tags',
            'summary' => 'summary',
            'description' => 'description',
            'externalDocs' => ['externalDocs', $this->one($this->parseExternalDocs(...))],
            'operationId' => 'operationId',
            'parameters' => ['parameters', fn (mixed $parameters, string $pointer): array => $this->list($parameters, $pointer, $this->parseParameter(...))],
            'requestBody' => ['requestBody', $this->one($this->parseRequestBody(...))],
            'responses' => ['responses', fn (mixed $responses, string $pointer): array => $this->keyedList($responses, $pointer, $this->parseResponse(...))],
            // compiled as they are, objects or arrays
            'callbacks' => 'callbacks',
            'deprecated' => 'deprecated',
            'security' => ['security', $this->parseSecurity(...)],
            'servers' => ['servers', fn (mixed $servers, string $pointer): array => $this->list($servers, $pointer, $this->parseServer(...))],
        ], ['path' => $path, 'webhook' => $webhook, 'method' => $method]);
    }

    /**
     * @param array<string, mixed> $parameter
     * @param array<string, mixed> $identity
     */
    protected function parseParameter(array $parameter, string $pointer, array $identity = []): OA\Parameter
    {
        if (isset($parameter['$ref'])) {
            return $this->build(OA\Parameter::class, $parameter, $pointer, ['$ref' => 'ref', 'summary' => 'summary', 'description' => 'description'], $identity);
        }

        return $this->build(OA\Parameter::class, $parameter, $pointer, [
            'name' => 'name',
            'in' => 'in',
            'description' => 'description',
            'required' => 'required',
            'deprecated' => 'deprecated',
            'allowEmptyValue' => 'allowEmptyValue',
            'style' => 'style',
            'explode' => 'explode',
            'allowReserved' => 'allowReserved',
            'schema' => ['schema', $this->oneSchema()],
            'example' => 'example',
            'examples' => ['examples', fn (mixed $examples, string $pointer): array => $this->keyedList($examples, $pointer, $this->parseExample(...))],
            'content' => ['content', fn (mixed $content, string $pointer): array => $this->keyedList($content, $pointer, $this->parseMediaType(...))],
        ], $identity);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $identity
     */
    protected function parseRequestBody(array $body, string $pointer, array $identity = []): OA\RequestBody
    {
        if (isset($body['$ref'])) {
            return $this->build(OA\RequestBody::class, $body, $pointer, ['$ref' => 'ref', 'summary' => 'summary', 'description' => 'description'], $identity);
        }

        return $this->build(OA\RequestBody::class, $body, $pointer, [
            'description' => 'description',
            'content' => ['content', fn (mixed $content, string $pointer): array => $this->keyedList($content, $pointer, $this->parseMediaType(...))],
            'required' => 'required',
        ], $identity);
    }

    /**
     * @param array<string, mixed> $response
     */
    protected function parseResponse(array $response, string $pointer, string|int|null $code = null, ?string $component = null): OA\Response
    {
        $identity = array_filter(['response' => $code, 'component' => $component], static fn (mixed $value): bool => $value !== null);

        if (isset($response['$ref'])) {
            return $this->build(OA\Response::class, $response, $pointer, ['$ref' => 'ref', 'summary' => 'summary', 'description' => 'description'], $identity);
        }

        return $this->build(OA\Response::class, $response, $pointer, [
            'description' => 'description',
            'headers' => ['headers', fn (mixed $headers, string $pointer): array => $this->keyedList($headers, $pointer, $this->parseHeader(...))],
            'content' => ['content', fn (mixed $content, string $pointer): array => $this->keyedList($content, $pointer, $this->parseMediaType(...))],
            'links' => ['links', fn (mixed $links, string $pointer): array => $this->keyedList($links, $pointer, $this->parseLink(...))],
        ], $identity);
    }

    /**
     * @param array<string, mixed> $header
     */
    protected function parseHeader(array $header, string $pointer, ?string $name = null, ?string $component = null): OA\Header
    {
        $identity = array_filter(['header' => $name, 'component' => $component], static fn (mixed $value): bool => $value !== null);

        if (isset($header['$ref'])) {
            return $this->build(OA\Header::class, $header, $pointer, ['$ref' => 'ref', 'summary' => 'summary', 'description' => 'description'], $identity);
        }

        return $this->build(OA\Header::class, $header, $pointer, [
            'description' => 'description',
            'required' => 'required',
            'deprecated' => 'deprecated',
            'style' => 'style',
            'explode' => 'explode',
            'schema' => ['schema', $this->oneSchema()],
            'example' => 'example',
            'examples' => ['examples', fn (mixed $examples, string $pointer): array => $this->keyedList($examples, $pointer, $this->parseExample(...))],
            'content' => ['content', fn (mixed $content, string $pointer): array => $this->keyedList($content, $pointer, $this->parseMediaType(...))],
        ], $identity);
    }

    /**
     * @param array<string, mixed> $mediaType
     */
    protected function parseMediaType(array $mediaType, string $pointer, string $type): OA\MediaType
    {
        return $this->build(OA\MediaType::class, $mediaType, $pointer, [
            'schema' => ['schema', $this->oneSchema()],
            'example' => 'example',
            'examples' => ['examples', fn (mixed $examples, string $pointer): array => $this->keyedList($examples, $pointer, $this->parseExample(...))],
            'encoding' => ['encoding', fn (mixed $encoding, string $pointer): array => $this->keyedList($encoding, $pointer, $this->parseEncoding(...))],
        ], ['mediaType' => $type]);
    }

    /**
     * @param array<string, mixed> $encoding
     */
    protected function parseEncoding(array $encoding, string $pointer, string $name): OA\Encoding
    {
        return $this->build(OA\Encoding::class, $encoding, $pointer, [
            'contentType' => 'contentType',
            'headers' => ['headers', fn (mixed $headers, string $pointer): array => $this->keyedList($headers, $pointer, $this->parseHeader(...))],
            'style' => 'style',
            'explode' => 'explode',
            'allowReserved' => 'allowReserved',
        ], ['encoding' => $name]);
    }

    /**
     * @param array<string, mixed> $link
     */
    protected function parseLink(array $link, string $pointer, ?string $name = null, ?string $component = null): OA\Link
    {
        $identity = array_filter(['link' => $name, 'component' => $component], static fn (mixed $value): bool => $value !== null);

        if (isset($link['$ref'])) {
            return $this->build(OA\Link::class, $link, $pointer, ['$ref' => 'ref', 'summary' => 'summary', 'description' => 'description'], $identity);
        }

        return $this->build(OA\Link::class, $link, $pointer, [
            'operationRef' => 'operationRef',
            'operationId' => 'operationId',
            'parameters' => 'parameters',
            'requestBody' => 'requestBody',
            'description' => 'description',
            'server' => ['server', $this->one($this->parseServer(...))],
        ], $identity);
    }

    /**
     * @param array<string, mixed> $example
     */
    protected function parseExample(array $example, string $pointer, ?string $name = null, ?string $component = null): OA\Example
    {
        $identity = array_filter(['example' => $name, 'component' => $component], static fn (mixed $value): bool => $value !== null);

        if (isset($example['$ref'])) {
            return $this->build(OA\Example::class, $example, $pointer, ['$ref' => 'ref', 'summary' => 'summary', 'description' => 'description'], $identity);
        }

        return $this->build(OA\Example::class, $example, $pointer, [
            'summary' => 'summary',
            'description' => 'description',
            'value' => 'value',
            'externalValue' => 'externalValue',
        ], $identity);
    }

    /**
     * @param array<string, mixed> $schema
     * @param array<string, mixed> $identity
     */
    protected function parseSchema(array $schema, string $pointer, array $identity = []): OA\Schema
    {
        $schemas = fn (mixed $list, string $pointer): array => $this->list($list, $pointer, $this->parseSchema(...), $this->schemaObject(...));
        $schemaMap = fn (mixed $map, string $pointer): array => $this->keyedMap($map, $pointer, fn (array $schema, string $pointer): OA\Schema => $this->parseSchema($schema, $pointer), $this->schemaObject(...));
        $schemaOrBool = fn (mixed $value, string $pointer): OA\Schema|bool|string => is_bool($value) ? $value : $this->oneSchema()($value, $pointer);

        return $this->build(OA\Schema::class, $schema, $pointer, [
            '$ref' => 'ref',
            'type' => 'type',
            'format' => 'format',
            'title' => 'title',
            'description' => 'description',
            'enum' => 'enum',
            'const' => 'const',
            'default' => 'default',
            'example' => 'example',
            'examples' => 'examples',

            'minLength' => 'minLength',
            'maxLength' => 'maxLength',
            'pattern' => 'pattern',
            'contentMediaType' => 'contentMediaType',
            'contentEncoding' => 'contentEncoding',
            'contentSchema' => ['contentSchema', $this->oneSchema()],

            'minimum' => 'minimum',
            'maximum' => 'maximum',
            'exclusiveMinimum' => 'exclusiveMinimum',
            'exclusiveMaximum' => 'exclusiveMaximum',
            'multipleOf' => 'multipleOf',

            'items' => ['items', $this->oneSchema()],
            'minItems' => 'minItems',
            'maxItems' => 'maxItems',
            'uniqueItems' => 'uniqueItems',
            'prefixItems' => ['prefixItems', $schemas],
            'contains' => ['contains', $schemaOrBool],
            'minContains' => 'minContains',
            'maxContains' => 'maxContains',
            'unevaluatedItems' => ['unevaluatedItems', $schemaOrBool],

            'properties' => ['properties', fn (mixed $properties, string $pointer): array => $this->keyedList($properties, $pointer, $this->parseProperty(...), $this->schemaObject(...))],
            'required' => 'required',
            'additionalProperties' => ['additionalProperties', $schemaOrBool],
            'patternProperties' => ['patternProperties', $schemaMap],
            'minProperties' => 'minProperties',
            'maxProperties' => 'maxProperties',
            'unevaluatedProperties' => ['unevaluatedProperties', $schemaOrBool],
            'propertyNames' => ['propertyNames', $this->oneSchema()],
            'dependentRequired' => 'dependentRequired',
            'dependentSchemas' => ['dependentSchemas', $schemaMap],

            'allOf' => ['allOf', $schemas],
            'anyOf' => ['anyOf', $schemas],
            'oneOf' => ['oneOf', $schemas],
            'not' => ['not', $this->oneSchema()],

            'if' => ['if', $this->oneSchema()],
            'then' => ['then', $this->oneSchema()],
            'else' => ['else', $this->oneSchema()],

            'deprecated' => 'deprecated',
            'readOnly' => 'readOnly',
            'writeOnly' => 'writeOnly',

            'discriminator' => ['discriminator', $this->one($this->parseDiscriminator(...))],
            'externalDocs' => ['externalDocs', $this->one($this->parseExternalDocs(...))],
            'xml' => ['xml', $this->one($this->parseXml(...))],
        ], $identity);
    }

    /**
     * @param array<string, mixed> $schema
     */
    protected function parseProperty(array $schema, string $pointer, string $name): OA\Property
    {
        return new OA\Property(property: $name, schema: $this->parseSchema($schema, $pointer));
    }

    /**
     * @param array<string, mixed> $discriminator
     */
    protected function parseDiscriminator(array $discriminator, string $pointer): OA\Discriminator
    {
        return $this->build(OA\Discriminator::class, $discriminator, $pointer, ['propertyName' => 'propertyName', 'mapping' => 'mapping']);
    }

    /**
     * @param array<string, mixed> $xml
     */
    protected function parseXml(array $xml, string $pointer): OA\Xml
    {
        return $this->build(OA\Xml::class, $xml, $pointer, [
            'name' => 'name',
            'namespace' => 'namespace',
            'prefix' => 'prefix',
            'attribute' => 'attribute',
            'wrapped' => 'wrapped',
        ]);
    }

    /**
     * Each component goes into its bucket, its name in `component`.
     *
     * A component path item keeps its own fields only: operations are filed by path, and one
     * that belongs to no path has nowhere to go.
     *
     * @param array<string, mixed> $components
     */
    protected function parseComponents(Specification $specification, array $components, string $pointer): void
    {
        $buckets = [
            'schemas' => fn (array $value, string $pointer, string $name): OA\Schema => $this->parseSchema($value, $pointer, ['component' => $name]),
            'responses' => fn (array $value, string $pointer, string $name): OA\Response => $this->parseResponse($value, $pointer, component: $name),
            'parameters' => fn (array $value, string $pointer, string $name): OA\Parameter => $this->parseParameter($value, $pointer, ['component' => $name]),
            'requestBodies' => fn (array $value, string $pointer, string $name): OA\RequestBody => $this->parseRequestBody($value, $pointer, ['component' => $name]),
            'headers' => fn (array $value, string $pointer, string $name): OA\Header => $this->parseHeader($value, $pointer, component: $name),
            'securitySchemes' => $this->parseSecurityScheme(...),
            'links' => fn (array $value, string $pointer, string $name): OA\Link => $this->parseLink($value, $pointer, component: $name),
            'examples' => fn (array $value, string $pointer, string $name): OA\Example => $this->parseExample($value, $pointer, component: $name),
            'pathItems' => $this->parseComponentPathItem(...),
        ];

        $unsupported = [];
        foreach ($components as $bucket => $members) {
            if (!isset($buckets[$bucket])) {
                $unsupported[] = $bucket;

                continue;
            }

            $object = $bucket === 'schemas' ? $this->schemaObject(...) : null;
            $specification->add(...$this->keyedList($members, $this->pointer($pointer, $bucket), $buckets[$bucket], $object));
        }

        $this->unsupported($pointer, $unsupported);
    }

    /**
     * @param array<string, mixed> $pathItem
     */
    protected function parseComponentPathItem(array $pathItem, string $pointer, string $name): OA\PathItem
    {
        $operations = array_values(array_intersect(array_keys($pathItem), static::OPERATION_METHODS));
        $this->unsupported($pointer, $operations);

        return $this->parsePathItem(array_diff_key($pathItem, array_flip($operations)), $pointer, ['component' => $name]);
    }

    /**
     * @param array<string, mixed> $scheme
     */
    protected function parseSecurityScheme(array $scheme, string $pointer, string $name): OA\Security\Scheme
    {
        if (isset($scheme['$ref'])) {
            return $this->build(OA\Security\Scheme::class, $scheme, $pointer, ['$ref' => 'ref', 'summary' => 'summary', 'description' => 'description'], ['component' => $name]);
        }

        return $this->build(OA\Security\Scheme::class, $scheme, $pointer, [
            'type' => 'type',
            'description' => 'description',
            'name' => 'name',
            'in' => 'in',
            'scheme' => 'scheme',
            'bearerFormat' => 'bearerFormat',
            'openIdConnectUrl' => 'openIdConnectUrl',
            'flows' => ['flows', fn (mixed $flows, string $pointer): array => $this->keyedList($flows, $pointer, $this->parseFlow(...))],
        ], ['component' => $name]);
    }

    /**
     * @param array<string, mixed> $flow
     */
    protected function parseFlow(array $flow, string $pointer, string $name): OA\Flow
    {
        return $this->build(OA\Flow::class, $flow, $pointer, [
            'authorizationUrl' => 'authorizationUrl',
            'tokenUrl' => 'tokenUrl',
            'refreshUrl' => 'refreshUrl',
            'scopes' => 'scopes',
        ], ['flow' => $name]);
    }

    /**
     * Each requirement as a `Requirement`: one scheme by `scheme`, several by `schemes`, none as empty.
     *
     * @return list<OA\Security\Requirement>
     */
    protected function parseSecurity(mixed $security, string $pointer): array
    {
        return $this->list($security, $pointer, static function (array $requirement): OA\Security\Requirement {
            if (count($requirement) === 1) {
                $scopes = reset($requirement);

                return new OA\Security\Requirement(scheme: key($requirement), scopes: is_array($scopes) ? array_values($scopes) : []);
            }

            return $requirement === [] ? new OA\Security\Requirement() : new OA\Security\Requirement(schemes: $requirement);
        });
    }

    /**
     * One object of `$class`, its constructor arguments read by the field map given.
     *
     * Each field maps to a constructor parameter that takes the value as it is, or to a parameter
     * and a reader `(value, pointer)` for a nested object or list. A field mapped to `null` is
     * read elsewhere. `x-` keys become `x`, and any other key is unsupported. `$identity` holds
     * what the object is filed under, which the document has as a key outside it.
     *
     * A value the parameter's type does not take is left out, with a warning, as is anything a
     * reader leaves out by returning `Undefined::UNDEFINED`.
     *
     * @template T of object
     *
     * @param class-string<T>                                    $class
     * @param array<string, mixed>                               $data
     * @param array<string, string|array{string, callable}|null> $fields
     * @param array<string, mixed>                               $identity
     *
     * @return T
     */
    protected function build(string $class, array $data, string $pointer, array $fields, array $identity = []): object
    {
        $types = $this->types($class);
        $arguments = $identity;
        $x = [];
        $unsupported = [];

        foreach ($data as $key => $value) {
            $key = (string) $key;

            if (str_starts_with($key, 'x-')) {
                $x[substr($key, 2)] = $value;
            } elseif (!array_key_exists($key, $fields)) {
                $unsupported[] = $key;
            } elseif ($fields[$key] !== null) {
                $nested = $this->pointer($pointer, $key);
                [$parameter, $argument] = is_string($fields[$key])
                    ? [$fields[$key], $this->typed($value, $types[$fields[$key]] ?? null, $nested)]
                    : [$fields[$key][0], $fields[$key][1]($value, $nested)];

                if ($argument !== Undefined::UNDEFINED) {
                    $arguments[$parameter] = $argument;
                }
            }
        }

        $this->unsupported($pointer, $unsupported);

        if ($x !== []) {
            $arguments['x'] = $x;
        }

        return new $class(...$arguments);
    }

    /**
     * The constructor parameters of `$class`, each with its type.
     *
     * @param class-string $class
     *
     * @return array<string, \ReflectionType|null>
     */
    protected function types(string $class): array
    {
        if (!isset($this->types[$class])) {
            $this->types[$class] = [];
            foreach ((new \ReflectionClass($class))->getConstructor()?->getParameters() ?? [] as $parameter) {
                $this->types[$class][$parameter->getName()] = $parameter->getType();
            }
        }

        return $this->types[$class];
    }

    /**
     * The value, if the type takes it.
     *
     * A number where only a string will do is read as that string, as YAML reads `version: 1.0`
     * as a float; a float is warned about, since `1.10` has already lost its zero by then.
     * Anything else is left out.
     */
    protected function typed(mixed $value, ?\ReflectionType $type, string $pointer): mixed
    {
        if (!$type instanceof \ReflectionType || $this->accepts($type, $value)) {
            return $value;
        }

        if ((is_int($value) || is_float($value)) && $this->accepts($type, '')) {
            $string = json_encode($value, JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);
            if (is_float($value)) {
                $this->logger->warning(sprintf('Expected a string at %s, read the number %s as "%s"', $this->location($pointer), $string, $string));
            }

            return $string;
        }

        $this->logger->warning(sprintf('Expected %s at %s, found %s, left out', $type, $this->location($pointer), get_debug_type($value)));

        return Undefined::UNDEFINED;
    }

    protected function accepts(\ReflectionType $type, mixed $value): bool
    {
        if ($value === null) {
            return $type->allowsNull();
        }

        foreach ($type instanceof \ReflectionUnionType ? $type->getTypes() : [$type] as $member) {
            if (!$member instanceof \ReflectionNamedType) {
                continue;
            }

            $name = $member->getName();
            $accepted = match ($name) {
                'mixed' => true,
                'string' => is_string($value),
                'int' => is_int($value),
                'float' => is_float($value) || is_int($value),
                'bool' => is_bool($value),
                'true' => $value === true,
                'false' => $value === false,
                'array', 'iterable' => is_array($value),
                default => $value instanceof $name,
            };

            if ($accepted) {
                return true;
            }
        }

        return false;
    }

    /**
     * A list of objects, each read by `$reader`; a member that is not an object is left out.
     *
     * @template T
     *
     * @param callable(array<string, mixed>, string): T             $reader
     * @param (\Closure(mixed, string): ?array<string, mixed>)|null $object what counts as an object, by default a map
     *
     * @return list<T>
     */
    protected function list(mixed $list, string $pointer, callable $reader, ?\Closure $object = null): array
    {
        $object ??= $this->object(...);

        $result = [];
        foreach ($this->listOf($list, $pointer) as $itemPointer => $item) {
            $item = $object($item, $itemPointer);
            if ($item !== null) {
                $result[] = $reader($item, $itemPointer);
            }
        }

        return $result;
    }

    /**
     * A map of objects read into a list, each reader told the key it was filed under.
     *
     * @template T
     *
     * @param callable(array<string, mixed>, string, string): T     $reader
     * @param (\Closure(mixed, string): ?array<string, mixed>)|null $object
     *
     * @return list<T>
     */
    protected function keyedList(mixed $map, string $pointer, callable $reader, ?\Closure $object = null): array
    {
        return array_values($this->keyedMap($map, $pointer, $reader, $object));
    }

    /**
     * A map of objects, each read by `$reader` and told its key, keyed as the document has it.
     *
     * @template T
     *
     * @param callable(array<string, mixed>, string, string): T     $reader
     * @param (\Closure(mixed, string): ?array<string, mixed>)|null $object
     *
     * @return array<string, T>
     */
    protected function keyedMap(mixed $map, string $pointer, callable $reader, ?\Closure $object = null): array
    {
        $object ??= $this->object(...);

        $result = [];
        foreach ($this->mapOf($map, $pointer) as [$key, $item, $itemPointer]) {
            $item = $object($item, $itemPointer);
            if ($item !== null) {
                $result[$key] = $reader($item, $itemPointer, $key);
            }
        }

        return $result;
    }

    /**
     * A reader for a field holding one object; anything else is warned about and left out.
     *
     * @template T
     *
     * @param callable(array<string, mixed>, string): T             $reader
     * @param (\Closure(mixed, string): ?array<string, mixed>)|null $object
     *
     * @return \Closure(mixed, string): (T|string)
     */
    protected function one(callable $reader, ?\Closure $object = null): \Closure
    {
        $object ??= $this->object(...);

        return static function (mixed $value, string $pointer) use ($reader, $object): mixed {
            $value = $object($value, $pointer);

            return $value === null ? Undefined::UNDEFINED : $reader($value, $pointer);
        };
    }

    /**
     * A reader for a field holding one schema.
     *
     * @return \Closure(mixed, string): (OA\Schema|string)
     */
    protected function oneSchema(): \Closure
    {
        return $this->one($this->parseSchema(...), $this->schemaObject(...));
    }

    /**
     * The members of a list, keyed by their pointer.
     *
     * @return array<string, mixed>
     */
    protected function listOf(mixed $list, string $pointer): array
    {
        if ($list === null) {
            return [];
        }

        if (!is_array($list) || !array_is_list($list)) {
            $this->logger->warning(sprintf('Expected a list at %s, skipped', $this->location($pointer)));

            return [];
        }

        $result = [];
        foreach ($list as $index => $item) {
            $result[$this->pointer($pointer, (string) $index)] = $item;
        }

        return $result;
    }

    /**
     * The members of a map, each with its key and its pointer.
     *
     * A list rather than a map, since PHP turns a key such as `200` back into an integer.
     *
     * @return list<array{string, mixed, string}>
     */
    protected function mapOf(mixed $map, string $pointer): array
    {
        if ($map === null || $map === []) {
            return [];
        }

        if (!is_array($map) || array_is_list($map)) {
            $this->logger->warning(sprintf('Expected a map at %s, skipped', $this->location($pointer)));

            return [];
        }

        $result = [];
        foreach ($map as $key => $item) {
            $result[] = [(string) $key, $item, $this->pointer($pointer, (string) $key)];
        }

        return $result;
    }

    /**
     * The value as an object, or `null`, warned about, if it is not one.
     *
     * @return array<string, mixed>|null
     */
    protected function object(mixed $value, string $pointer): ?array
    {
        if (is_array($value) && ($value === [] || !array_is_list($value))) {
            return $value;
        }

        $this->logger->warning(sprintf('Expected an object at %s, left out', $this->location($pointer)));

        return null;
    }

    /**
     * The value as a schema object: `true` is the empty schema, and `false`, which the model has
     * no way to say where a `Schema` is expected, is warned about and left out.
     *
     * @return array<string, mixed>|null
     */
    protected function schemaObject(mixed $value, string $pointer): ?array
    {
        if ($value === true) {
            return [];
        }

        if ($value === false) {
            $this->logger->warning(sprintf('The schema false is not supported, left out at %s', $this->location($pointer)));

            return null;
        }

        return $this->object($value, $pointer);
    }

    /**
     * @param list<string> $keys
     */
    protected function unsupported(string $pointer, array $keys): void
    {
        foreach ($keys as $key) {
            $this->logger->warning(sprintf('"%s" is not supported, left out at %s', $key, $this->location($pointer)));
        }
    }

    protected function pointer(string $pointer, string $key): string
    {
        return $pointer . '/' . str_replace(['~', '/'], ['~0', '~1'], $key);
    }

    protected function location(string $pointer): string
    {
        return '#' . $pointer;
    }
}
