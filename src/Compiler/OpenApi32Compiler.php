<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Compiler;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Undefined;

/**
 * Compiles a Specification into an OpenAPI 3.2.x document array.
 *
 * 3.2 is a superset of 3.1.
 */
class OpenApi32Compiler extends OpenApi31Compiler
{
    protected const VERSION_PREFIX = '3.2.';

    protected const OPERATION_METHODS = [...parent::OPERATION_METHODS, 'query'];

    protected const OPERATION_REQUEST_BODY_METHODS = [...parent::OPERATION_REQUEST_BODY_METHODS, 'query'];

    protected const UNSUPPORTED_PROPERTIES = [];

    protected const UNSUPPORTED_VALUES = [];

    public function getVersion(): string
    {
        return '3.2.0';
    }

    #[\Override]
    protected function validateSpecification(Specification $specification): void
    {
        parent::validateSpecification($specification);

        $this->validateMetadataUrls($specification);
        $this->validatePositionalEncodings($specification);
        $this->validateQuerystringParameters($specification);
        $this->validateQuerystringOperations($specification);
        $this->validateTagParents($specification);
    }

    /**
     * A tag's `parent` names another tag of the document.
     */
    protected function validateTagParents(Specification $specification): void
    {
        $tagNames = array_map(fn (OA\Tag $tag): ?string => $tag->name, $specification->tags);

        foreach ($specification->tags as $tag) {
            if ($tag->parent !== null && !in_array($tag->parent, $tagNames, true)) {
                $this->logger->error('Tag "' . $tag->name . '" names parent "' . $tag->parent . '", which does not exist in ' . $tag->getSourceLocation());
            }
        }
    }

    /**
     * A `parent` that names no tag of the document is left out.
     */
    #[\Override]
    protected function compileTags(array $tags): array
    {
        $tagNames = array_map(fn (OA\Tag $tag): ?string => $tag->name, $tags);

        return array_map(function (OA\Tag $tag) use ($tagNames): array {
            $compiled = $this->compileTag($tag);
            if (isset($compiled['parent']) && !in_array($compiled['parent'], $tagNames, true)) {
                unset($compiled['parent']);
            }

            return $compiled;
        }, $tags);
    }

    /**
     * Only an `oauth2` scheme takes `oauth2MetadataUrl`, so on any other type it is left out.
     */
    protected function validateMetadataUrls(Specification $specification): void
    {
        $this->visit($specification, OA\Security\Scheme::class, function (OA\Security\Scheme $scheme): void {
            if ($scheme->oauth2MetadataUrl !== null && $scheme->type !== OA\SchemeType::OAuth2->value) {
                $this->logger->warning("{$this->subject($scheme)} uses oauth2MetadataUrl, which only an oauth2 scheme takes, omitted"
                    . $this->attributeLocation($scheme));
            }
        });
    }

    /**
     * `value` is the pre-3.2 way of embedding an example, and is mutually exclusive with both
     * of the fields that replace it.
     *
     * `serializedValue` and `externalValue` overlap as well:
     * both describe the example after serialization.
     */
    #[\Override]
    protected function validateExamples(Specification $specification): void
    {
        $this->visit($specification, OA\Example::class, function (OA\Example $example): void {
            $subject = $example->example === null ? 'Example' : 'Example "' . $example->example . '"';

            if ($example->value !== Undefined::UNDEFINED && ($example->dataValue !== Undefined::UNDEFINED || $example->serializedValue !== null)) {
                $this->logger->error("{$subject} value is mutually exclusive with dataValue and serializedValue" . $this->nestedLocation($example));
            }

            if ($example->serializedValue !== null && $example->externalValue !== null) {
                $this->logger->error("{$subject} serializedValue and externalValue are mutually exclusive" . $this->nestedLocation($example));
            }
        });
    }

    /**
     * A response has a summary of its own from 3.2 on.
     *
     * Its headers and links still do not.
     */
    #[\Override]
    protected function summariesWithoutRef(array $attributes): array
    {
        $subjects = [];
        foreach ($attributes as $attribute) {
            if ($attribute instanceof OA\Response && $attribute->getRefValue() === null) {
                $checked = [...$attribute->headers ?? [], ...$attribute->links ?? []];
            } else {
                $checked = [$attribute];
            }

            $subjects = [...$subjects, ...parent::summariesWithoutRef($checked)];
        }

        return $subjects;
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileExample(OA\Example $example): array
    {
        if ($example->ref !== null) {
            return $this->compileReference($example);
        }

        $result = $this->filter([
            'summary' => $example->summary,
            'description' => $example->description,
            'externalValue' => $example->externalValue,
            'serializedValue' => $example->serializedValue,
        ], $example);

        return $this->withDefined($result, ['value' => $example->value, 'dataValue' => $example->dataValue]);
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileResponse(OA\Response $response): array
    {
        $result = parent::compileResponse($response);
        if ($response->ref !== null) {
            return $result;
        }

        return $this->filter(['summary' => $response->summary]) + $result;
    }

    /**
     * Encodings addressed by position, `prefixEncoding` and `itemEncoding`, are the alternative to
     * `encoding` by name, and need a schema with items for their positions.
     *
     * That schema is `itemSchema` or an array `schema`. A name on one of them has no effect.
     */
    protected function validatePositionalEncodings(Specification $specification): void
    {
        $check = function (OA\MediaType|OA\Encoding $holder): void {
            $positional = array_filter([...$holder->prefixEncoding ?? [], $holder->itemEncoding], static fn (mixed $encoding): bool => $encoding instanceof OA\Encoding);
            if ($positional === []) {
                return;
            }

            $subject = $this->subject($holder);
            $location = $this->attributeLocation($holder);
            $named = $holder instanceof OA\MediaType ? $holder->encoding : $holder->encodings;
            if ($named) {
                $this->logger->error("{$subject} uses encoding beside prefixEncoding or itemEncoding, which exclude each other{$location}");
            }

            if ($holder instanceof OA\MediaType && !$holder->itemSchema instanceof OA\Schema && !$this->hasItems($holder->schema)) {
                $this->logger->error("{$subject} uses prefixEncoding or itemEncoding without itemSchema or an array schema{$location}");
            }

            foreach ($positional as $encoding) {
                if ($encoding->encoding !== null) {
                    $this->logger->warning("Encoding \"{$encoding->encoding}\" is addressed by position in {$subject}, its name ignored{$location}");
                }
            }
        };

        $this->visit($specification, [OA\MediaType::class, OA\Encoding::class], $check);
    }

    /**
     * Whether a schema can describe items by position.
     *
     * A reference is taken on trust: what it points to is checked where it is declared.
     */
    protected function hasItems(?OA\Schema $schema): bool
    {
        return $schema instanceof OA\Schema
            && ($schema->getRefValue() !== null || $this->isArrayType($schema));
    }

    /**
     * A querystring parameter is serialized by its media type, so it takes `content` and none of
     * the fields that serialize a `schema`.
     */
    protected function validateQuerystringParameters(Specification $specification): void
    {
        $this->visit($specification, OA\Parameter::class, function (OA\Parameter $parameter): void {
            if ($parameter->in !== OA\ParameterIn::Querystring->value || $parameter->ref !== null) {
                return;
            }

            $subject = 'Querystring parameter "' . ($parameter->name ?? $parameter->component) . '"';
            $location = $this->attributeLocation($parameter);
            if (!$parameter->content) {
                $this->logger->error("{$subject} must have content{$location}");
            }

            $serializing = array_keys(array_filter([
                'schema' => $parameter->schema,
                'style' => $parameter->style,
                'explode' => $parameter->explode,
                'allowReserved' => $parameter->allowReserved,
            ], static fn (mixed $value): bool => $value !== null));
            if ($serializing !== []) {
                $this->logger->error("{$subject} must not use " . implode(', ', $serializing) . $location);
            }
        });
    }

    /**
     * An operation has at most one querystring parameter, and none beside a query parameter.
     *
     * The parameters of its path item count, and an operation's own replaces one with the same
     * name and location.
     */
    protected function validateQuerystringOperations(Specification $specification): void
    {
        $shared = [];
        foreach ($specification->pathItems as $pathItem) {
            if ($pathItem->path !== null && $pathItem->component === null) {
                $shared[$pathItem->path] = [...$shared[$pathItem->path] ?? [], ...$pathItem->parameters ?? []];
            }
        }

        $this->visit($specification, OA\Operation::class, function (OA\Operation $operation) use ($shared): void {
            $locations = [];
            $pathParameters = $operation->path !== null ? $shared[$operation->path] ?? [] : [];
            foreach ([...$pathParameters, ...$operation->parameters ?? []] as $parameter) {
                $locations[$parameter->name . '|' . $parameter->in] = $parameter->in;
            }

            $counts = array_count_values(array_filter($locations, is_string(...)));
            $querystrings = $counts[OA\ParameterIn::Querystring->value] ?? 0;
            if ($querystrings === 0) {
                return;
            }

            $subject = 'Operation "' . ($operation->operationId ?? strtoupper((string) $operation->method) . ' ' . $operation->path) . '"';
            if ($querystrings > 1) {
                $this->logger->error("{$subject} has more than one querystring parameter in " . $operation->getSourceLocation());
            }

            if (isset($counts[OA\ParameterIn::Query->value])) {
                $this->logger->error("{$subject} has a querystring parameter beside query parameters in " . $operation->getSourceLocation());
            }
        });
    }

    /**
     * A method without a field of its own goes into `additionalOperations`, keyed by the method as
     * it is sent.
     *
     * Methods are kept lowercase, so the key is the uppercase form, such as `COPY`.
     *
     * @param  array<string,mixed> $item
     * @return array<string,mixed>
     */
    #[\Override]
    protected function addOperation(array $item, OA\Operation $operation): array
    {
        if ($operation->method === null || in_array($operation->method, static::OPERATION_METHODS, true) || !$this->supportsMethod($operation->method)) {
            return parent::addOperation($item, $operation);
        }

        $item['additionalOperations'][strtoupper($operation->method)] = $this->compileOperation($operation);

        return $item;
    }

    /**
     * Any HTTP method, as a token per RFC 9110.
     */
    #[\Override]
    protected function supportsMethod(string $method): bool
    {
        return preg_match('/^[!#$%&\'*+.^_`|~0-9a-z-]+$/', $method) === 1;
    }

    /**
     * HTTP leaves the body of a method without a field of its own to the method, so it is kept.
     */
    #[\Override]
    protected function acceptsRequestBody(string $method): bool
    {
        return parent::acceptsRequestBody($method) || !in_array($method, static::OPERATION_METHODS, true);
    }

    /**
     * @return array<string,mixed>
     */
    #[\Override]
    protected function compileXml(OA\Xml $xml, ?OA\Schema $schema = null): array
    {
        return ($xml->nodeType !== null ? ['nodeType' => $xml->nodeType] : []) + parent::compileXml($xml, $schema);
    }

    /**
     * 3.2 writes `nodeType` itself, so nothing is translated.
     *
     * @return array<string,bool>
     */
    #[\Override]
    protected function nodeTypeFields(OA\Xml $xml, ?OA\Schema $schema): array
    {
        return [];
    }

    /**
     * `nodeType` replaces `attribute` and `wrapped`, which must not appear beside it.
     */
    #[\Override]
    protected function validateXmlNodeTypes(Specification $specification): void
    {
        $this->visit($specification, OA\Xml::class, function (OA\Xml $xml): void {
            if ($xml->nodeType === null) {
                return;
            }

            $replaced = array_keys(array_filter(['attribute' => $xml->attribute, 'wrapped' => $xml->wrapped], static fn (?bool $value): bool => $value !== null));
            if ($replaced !== []) {
                $this->logger->error('Xml uses nodeType beside ' . implode(' and ', $replaced) . ', which it replaces'
                    . $this->attributeLocation($xml));
            }
        });
    }
}
