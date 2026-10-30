<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Type\TypeMapper;
use OpenApi\Undefined;
use OpenApi\Utils\DocBlockParser;
use OpenApi\Utils\PipeInterface;

/**
 * Fills summary, description, and deprecated from PHP docblock comments.
 *
 * Walks all attributes in the specification that have summary/description
 * properties and populates them from the reflector's docblock when not
 * explicitly set.
 *
 * @implements PipeInterface<Specification>
 */
class Docblocks implements PipeInterface
{
    public function __construct(
        protected DocBlockParser $parser = new DocBlockParser(),
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->operations as $operation) {
            $this->augmentSummaryAndDescription($operation);
            $this->augmentDeprecated($operation);
            $this->augmentOperationParameters($operation);
            $this->augmentResponses($operation->responses ?? []);
            $this->augmentMediaTypes($operation->requestBody->content ?? []);
        }

        foreach ($payload->schemas as $schema) {
            $this->augmentSchema($schema);
        }

        $this->augmentParameters($payload->parameters);
        foreach ($payload->requestBodies as $requestBody) {
            $this->augmentMediaTypes($requestBody->content ?? []);
        }

        $this->augmentResponses($payload->responses);
        $this->augmentHeaders($payload->headers);
        $this->augmentMediaTypes($payload->mediaTypes);

        return null;
    }

    /**
     * Override the docblock parser used to extract summaries and descriptions.
     */
    public function setParser(DocBlockParser $parser): static
    {
        $this->parser = $parser;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    protected function augmentSummaryAndDescription(OA\Operation $operation): void
    {
        if (!Undefined::isDefault($operation->summary) && !Undefined::isDefault($operation->description)) {
            return;
        }

        $docblock = $this->getDocComment($operation);
        if ($docblock === null) {
            return;
        }

        $content = $this->parser->parseDocblock($docblock);
        if ($content === '' || Undefined::isDefault($content)) {
            return;
        }

        if (Undefined::isDefault($operation->summary) && Undefined::isDefault($operation->description)) {
            $operation->summary = $this->extractSummary($content);
            $operation->description = $this->extractDescription($content);
        } elseif (Undefined::isDefault($operation->summary)) {
            $operation->summary = $content;
        } elseif (Undefined::isDefault($operation->description)) {
            $operation->description = $content;
        }
    }

    protected function augmentDescription(OA\Schema $schema): void
    {
        if (!Undefined::isDefault($schema->description)) {
            return;
        }

        $docblock = $this->getDocComment($schema);
        if ($docblock === null) {
            return;
        }

        $content = $this->parser->parseDocblock($docblock);
        if ($content === '' || Undefined::isDefault($content)) {
            return;
        }

        $schema->description = $content;
    }

    protected function augmentDeprecated(OA\Operation|OA\Schema $attribute): void
    {
        if ($attribute->deprecated !== null) {
            return;
        }

        $docblock = $this->getDocComment($attribute);
        if ($docblock === null) {
            return;
        }

        if ($this->parser->isDeprecated($docblock)) {
            $attribute->deprecated = true;
        }
    }

    protected function augmentOperationParameters(OA\Operation $operation): void
    {
        if (!$operation->parameters) {
            return;
        }

        $methodDoc = $this->getDocComment($operation);
        $paramTags = [];
        if ($methodDoc !== null) {
            $this->parser->parseDocblock($methodDoc, $paramTags);
        }
        $paramDescriptions = $paramTags['param'] ?? [];

        $this->augmentParameters($operation->parameters, $paramDescriptions);
    }

    /**
     * @param list<OA\Parameter>                                        $parameters
     * @param array<string, array{type: ?string, description: ?string}> $parentParamTags
     */
    protected function augmentParameters(array $parameters, array $parentParamTags = []): void
    {
        foreach ($parameters as $parameter) {
            $this->augmentSchema($parameter->schema);
            $this->augmentMediaTypes($parameter->content ?? []);
            if (!$parameter->getReflector() instanceof \ReflectionProperty) {
                $this->augmentParameterDescription($parameter, $parentParamTags);

                continue;
            }

            $this->augmentPropertyDocumentation($parameter);
        }
    }

    /**
     * @param list<OA\Response> $responses
     */
    protected function augmentResponses(array $responses): void
    {
        foreach ($responses as $response) {
            $this->augmentHeaders($response->headers ?? []);
            $this->augmentMediaTypes($response->content ?? []);
        }
    }

    /**
     * @param list<OA\Header> $headers
     */
    protected function augmentHeaders(array $headers): void
    {
        foreach ($headers as $header) {
            $this->augmentSchema($header->schema);
            $this->augmentMediaTypes($header->content ?? []);
            $this->augmentPropertyDocumentation($header);
        }
    }

    protected function augmentPropertyDocumentation(OA\Parameter|OA\Header|OA\Schema $attribute): void
    {
        if (!$attribute->getReflector() instanceof \ReflectionProperty) {
            return;
        }

        $docblock = $this->getDocComment($attribute);
        if ($docblock === null) {
            return;
        }

        if ($attribute instanceof OA\Header ? $attribute->description === null : Undefined::isDefault($attribute->description)) {
            $content = $this->parser->parseDocblock($docblock);
            if ($content !== '' && !Undefined::isDefault($content)) {
                $attribute->description = $content;
            } else {
                /*
                 * Descriptions embedded in @var tags need parseVarLine();
                 * parseDocblock() only extracts free text before tags.
                 */
                $varLine = $this->parser->parseVarLine($docblock);
                if ($varLine['description'] !== null) {
                    $attribute->description = $varLine['description'];
                }
            }
        }

        if ($attribute->deprecated === null && $this->parser->isDeprecated($docblock)) {
            $attribute->deprecated = true;
        }

        if (Undefined::isDefault($attribute->example) && !$attribute->examples) {
            $schema = $attribute instanceof OA\Schema ? $attribute : ($attribute->schema ?? new OA\Schema());
            $attribute->example = $this->extractExample($schema, $docblock);
        }
    }

    /**
     * @param array<string, array{type: ?string, description: ?string}> $parentParamTags
     */
    protected function augmentParameterDescription(OA\Parameter $parameter, array $parentParamTags = []): void
    {
        if (!Undefined::isDefault($parameter->description)) {
            return;
        }

        $reflector = $parameter->getReflector();

        if ($reflector instanceof \ReflectionParameter) {
            $name = $reflector->getName();

            // PHP 8.6+ exposes docblocks on parameters directly
            if (method_exists($reflector, 'getDocComment') && ($doc = $reflector->getDocComment()) !== false) {
                $varLine = $this->parser->parseVarLine($doc);
                if ($varLine['description'] !== null) {
                    $parameter->description = $varLine['description'];

                    return;
                }
            }

            if (isset($parentParamTags[$name]) && $parentParamTags[$name]['description'] !== null) {
                $parameter->description = $parentParamTags[$name]['description'];

                return;
            }

            $method = $reflector->getDeclaringFunction();
            if ($method instanceof \ReflectionMethod || $method instanceof \ReflectionFunction) {
                $tags = [];
                $doc = $method->getDocComment();
                if ($doc !== false) {
                    $this->parser->parseDocblock($doc, $tags);
                    $params = $tags['param'] ?? [];
                    if (isset($params[$name]) && $params[$name]['description'] !== null) {
                        $parameter->description = $params[$name]['description'];
                    }
                }
            }

            return;
        }

        if ($parameter->name !== null && isset($parentParamTags[$parameter->name]) && $parentParamTags[$parameter->name]['description'] !== null) {
            $parameter->description = $parentParamTags[$parameter->name]['description'];
        }
    }

    /**
     * @param list<OA\MediaType> $mediaTypes
     */
    protected function augmentMediaTypes(array $mediaTypes): void
    {
        foreach ($mediaTypes as $mediaType) {
            $this->augmentSchema($mediaType->schema);
        }
    }

    protected function augmentSchema(?OA\Schema $schema): void
    {
        if (!$schema instanceof OA\Schema) {
            return;
        }

        if ($schema->getReflector() instanceof \ReflectionProperty) {
            $this->augmentPropertyDocumentation($schema);
        } else {
            $this->augmentDescription($schema);
            $this->augmentDeprecated($schema);
        }
        $this->augmentProperties($schema);

        foreach (['items', 'additionalProperties', 'contains', 'unevaluatedItems', 'unevaluatedProperties', 'propertyNames', 'not', 'if', 'then', 'else', 'contentSchema'] as $field) {
            if ($schema->{$field} instanceof OA\Schema) {
                $this->augmentSchema($schema->{$field});
            }
        }
        foreach (['allOf', 'anyOf', 'oneOf', 'prefixItems', 'patternProperties', 'dependentSchemas'] as $field) {
            foreach ($schema->{$field} ?? [] as $child) {
                $this->augmentSchema($child);
            }
        }
    }

    protected function augmentProperties(OA\Schema $schema): void
    {
        $properties = $schema->properties ?? [];

        foreach ($properties as $property) {
            if (!$property instanceof OA\Property) {
                continue;
            }

            $property->schema ??= new OA\Schema();

            // Schema may have its own reflector (inline), otherwise fall back to
            // the property's reflector (e.g. class constants where schema is synthetic)
            $docblock = $this->getDocComment($property->schema) ?? $this->getDocComment($property);

            if ($docblock) {
                if (Undefined::isDefault($property->schema->description)) {
                    $content = $this->parser->parseDocblock($docblock);
                    if ($content !== '' && !Undefined::isDefault($content)) {
                        $property->schema->description = $content;
                    } else {
                        // parseDocblock() only extracts free text before tags;
                        // descriptions embedded in @var tags (e.g. "@var string The name") need parseVarLine()
                        $varLine = $this->parser->parseVarLine($docblock);
                        if ($varLine['description'] !== null) {
                            $property->schema->description = $varLine['description'];
                        }
                    }
                }

                if ($property->schema->deprecated === null && $this->parser->isDeprecated($docblock)) {
                    $property->schema->deprecated = true;
                }

                if (Undefined::isDefault($property->schema->example)) {
                    $property->schema->example = $this->extractExample($property->schema, $docblock);
                }
            }
            $this->augmentSchema($property->schema);
        }
    }

    protected function getDocComment(AttributeInterface $attribute): ?string
    {
        $reflector = $attribute->getReflector();
        if (!$reflector instanceof \Reflector) {
            return null;
        }

        if ($reflector instanceof \ReflectionParameter && $reflector->isPromoted()) {
            $function = $reflector->getDeclaringFunction();
            $class = $function instanceof \ReflectionMethod ? $function->getDeclaringClass() : null;
            if ($class?->hasProperty($reflector->getName())) {
                $reflector = $class->getProperty($reflector->getName());
            }
        }

        if (!method_exists($reflector, 'getDocComment')) {
            return null;
        }

        $doc = $reflector->getDocComment();

        return $doc !== false ? $doc : null;
    }

    protected function extractSummary(string $content): ?string
    {
        $summary = $this->parser->extractCommentSummary($content);

        return Undefined::isDefault($summary) ? null : $summary;
    }

    protected function extractDescription(string $content): ?string
    {
        $description = $this->parser->extractCommentDescription($content);

        return Undefined::isDefault($description) ? null : $description;
    }

    protected function extractExample(OA\Schema $schema, string $docblock): mixed
    {
        $example = $this->parser->extractExample($docblock);
        if ($example === null) {
            return Undefined::UNDEFINED;
        }

        $types = (array) $schema->type;
        if ($types === [] || in_array('string', $types, true)) {
            return $example;
        }

        try {
            $value = json_decode($example, flags: JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
            $decoded = json_decode($example, flags: JSON_THROW_ON_ERROR);

            /*
             * Reject non-finite numbers and integers that would become strings, including nested values.
             */
            if (json_encode($value, JSON_THROW_ON_ERROR) !== json_encode($decoded, JSON_THROW_ON_ERROR)) {
                return $example;
            }
        } catch (\JsonException) {
            return $example;
        }

        $valueType = match (true) {
            $value === null => 'null',
            $value instanceof \stdClass => 'object',
            is_float($value) && floor($value) === $value => 'integer',
            default => TypeMapper::valueType($value),
        };

        if (in_array($valueType, $types, true)
            || ($valueType === 'integer' && in_array('number', $types, true))
            || ($valueType === 'null' && $schema->nullable === true)
        ) {
            return $value;
        }

        return $example;
    }
}
