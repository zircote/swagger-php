<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * Traversal helpers for walking the Specification tree.
 */
class SpecificationWalker
{
    /** @var \SplObjectStorage<OA\Schema, true> */
    private \SplObjectStorage $visited;

    public function __construct(
        protected readonly Specification $specification,
    ) {
    }

    /**
     * Walk every Schema in the specification, recursively into nested schemas.
     *
     * @param callable(OA\Schema): void $visitor
     */
    public function eachSchema(callable $visitor): void
    {
        $this->walk($visitor, null);
    }

    /**
     * Walk every ref-bearing attribute in the specification.
     *
     * @param callable(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme): void $visitor
     */
    public function eachRef(callable $visitor): void
    {
        $this->walk(null, $visitor);
    }

    /**
     * Single traversal driving both schema and ref visitors.
     *
     * @param (callable(OA\Schema): void)|null                                                                                         $schemaVisitor
     * @param (callable(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme): void)|null $refVisitor
     */
    protected function walk(?callable $schemaVisitor, ?callable $refVisitor): void
    {
        $this->visited = new \SplObjectStorage();

        foreach ($this->specification->schemas as $schema) {
            $this->walkSchemaTree($schema, $schemaVisitor, $refVisitor);
        }

        foreach ($this->specification->operations as $operation) {
            $this->walkOperation($operation, $schemaVisitor, $refVisitor);
        }

        foreach ($this->specification->pathItems as $pathItem) {
            $this->walkParameters($pathItem->parameters, $schemaVisitor, $refVisitor);
            $this->walkResponses($pathItem->responses, $schemaVisitor, $refVisitor);
        }

        foreach ($this->specification->parameters as $parameter) {
            $this->walkParameter($parameter, $schemaVisitor, $refVisitor);
        }

        foreach ($this->specification->requestBodies as $body) {
            if ($refVisitor) {
                $this->visitRef($body, $refVisitor);
            }
            $this->walkMediaTypes($body->content, $schemaVisitor, $refVisitor);
        }

        foreach ($this->specification->responses as $response) {
            $this->walkResponse($response, $schemaVisitor, $refVisitor);
        }

        foreach ($this->specification->headers as $header) {
            $this->walkHeader($header, $schemaVisitor, $refVisitor);
        }

        if ($refVisitor) {
            foreach ($this->specification->links as $link) {
                $this->visitRef($link, $refVisitor);
            }

            foreach ($this->specification->examples as $example) {
                $this->visitRef($example, $refVisitor);
            }

            if ($this->specification->openapi->security) {
                $this->walkSecurityRefs($this->specification->openapi->security, $refVisitor);
            }
        }

        unset($this->visited);
    }

    protected function walkOperation(OA\Operation $operation, ?callable $schemaVisitor, ?callable $refVisitor): void
    {
        $this->walkParameters($operation->parameters, $schemaVisitor, $refVisitor);

        if ($operation->requestBody instanceof OA\RequestBody) {
            if ($refVisitor) {
                $this->visitRef($operation->requestBody, $refVisitor);
            }
            $this->walkMediaTypes($operation->requestBody->content, $schemaVisitor, $refVisitor);
        }

        $this->walkResponses($operation->responses, $schemaVisitor, $refVisitor);

        if ($refVisitor && $operation->security) {
            $this->walkSecurityRefs($operation->security, $refVisitor);
        }

        if ($operation->callbacks) {
            foreach ($operation->callbacks as $callback) {
                if (is_array($callback)) {
                    array_walk_recursive($callback, function (mixed $value) use ($schemaVisitor, $refVisitor): void {
                        if ($value instanceof OA\Operation) {
                            $this->walkOperation($value, $schemaVisitor, $refVisitor);
                        }
                    });
                }
            }
        }
    }

    /**
     * @param list<OA\Parameter>|null $parameters
     */
    protected function walkParameters(?array $parameters, ?callable $schemaVisitor, ?callable $refVisitor): void
    {
        if (!$parameters) {
            return;
        }

        foreach ($parameters as $parameter) {
            $this->walkParameter($parameter, $schemaVisitor, $refVisitor);
        }
    }

    protected function walkParameter(OA\Parameter $parameter, ?callable $schemaVisitor, ?callable $refVisitor): void
    {
        if ($refVisitor) {
            $this->visitRef($parameter, $refVisitor);
            $this->walkExampleRefs($parameter->examples, $refVisitor);
        }
        if ($parameter->schema instanceof OA\Schema) {
            $this->walkSchemaTree($parameter->schema, $schemaVisitor, $refVisitor);
        }
    }

    /**
     * @param list<OA\Response>|null $responses
     */
    protected function walkResponses(?array $responses, ?callable $schemaVisitor, ?callable $refVisitor): void
    {
        if (!$responses) {
            return;
        }

        foreach ($responses as $response) {
            $this->walkResponse($response, $schemaVisitor, $refVisitor);
        }
    }

    protected function walkResponse(OA\Response $response, ?callable $schemaVisitor, ?callable $refVisitor): void
    {
        if ($refVisitor) {
            $this->visitRef($response, $refVisitor);
        }
        $this->walkMediaTypes($response->content, $schemaVisitor, $refVisitor);

        if ($response->headers) {
            foreach ($response->headers as $header) {
                $this->walkHeader($header, $schemaVisitor, $refVisitor);
            }
        }

        if ($refVisitor && $response->links) {
            foreach ($response->links as $link) {
                $this->visitRef($link, $refVisitor);
            }
        }
    }

    protected function walkHeader(OA\Header $header, ?callable $schemaVisitor, ?callable $refVisitor): void
    {
        if ($refVisitor) {
            $this->visitRef($header, $refVisitor);
            $this->walkExampleRefs($header->examples, $refVisitor);
        }
        if ($header->schema instanceof OA\Schema) {
            $this->walkSchemaTree($header->schema, $schemaVisitor, $refVisitor);
        }
    }

    /**
     * @param list<OA\MediaType>|null $mediaTypes
     */
    protected function walkMediaTypes(?array $mediaTypes, ?callable $schemaVisitor, ?callable $refVisitor): void
    {
        if (!$mediaTypes) {
            return;
        }

        foreach ($mediaTypes as $mediaType) {
            if ($mediaType->schema instanceof OA\Schema) {
                $this->walkSchemaTree($mediaType->schema, $schemaVisitor, $refVisitor);
            }
            if ($refVisitor) {
                $this->walkExampleRefs($mediaType->examples, $refVisitor);
            }
        }
    }

    protected function walkSchemaTree(OA\Schema $schema, ?callable $schemaVisitor, ?callable $refVisitor): void
    {
        if ($this->visited->contains($schema)) {
            return;
        }
        $this->visited->attach($schema);

        if ($schemaVisitor) {
            $schemaVisitor($schema);
        }
        if ($refVisitor) {
            $this->visitRef($schema, $refVisitor);
            if ($schema->discriminator instanceof OA\Discriminator && $schema->discriminator->mapping !== null) {
                foreach ($schema->discriminator->mapping as $ref) {
                    $refVisitor(new OA\Schema(ref: $ref));
                }
            }
        }

        if ($schema->properties) {
            foreach ($schema->properties as $property) {
                if ($property instanceof OA\Property && $property->schema instanceof OA\Schema) {
                    $this->walkSchemaTree($property->schema, $schemaVisitor, $refVisitor);
                } elseif ($property instanceof OA\Schema) {
                    $this->walkSchemaTree($property, $schemaVisitor, $refVisitor);
                }
            }
        }

        if ($schema->items instanceof OA\Schema) {
            $this->walkSchemaTree($schema->items, $schemaVisitor, $refVisitor);
        }
        if ($schema->additionalProperties instanceof OA\Schema) {
            $this->walkSchemaTree($schema->additionalProperties, $schemaVisitor, $refVisitor);
        }

        foreach ($schema->allOf ?? [] as $child) {
            $this->walkSchemaTree($child, $schemaVisitor, $refVisitor);
        }
        foreach ($schema->anyOf ?? [] as $child) {
            $this->walkSchemaTree($child, $schemaVisitor, $refVisitor);
        }
        foreach ($schema->oneOf ?? [] as $child) {
            $this->walkSchemaTree($child, $schemaVisitor, $refVisitor);
        }
        if ($schema->not instanceof OA\Schema) {
            $this->walkSchemaTree($schema->not, $schemaVisitor, $refVisitor);
        }

        // JSON Schema 2020-12 keywords
        foreach ($schema->prefixItems ?? [] as $child) {
            $this->walkSchemaTree($child, $schemaVisitor, $refVisitor);
        }
        if ($schema->contains instanceof OA\Schema) {
            $this->walkSchemaTree($schema->contains, $schemaVisitor, $refVisitor);
        }
        if ($schema->unevaluatedItems instanceof OA\Schema) {
            $this->walkSchemaTree($schema->unevaluatedItems, $schemaVisitor, $refVisitor);
        }
        if ($schema->unevaluatedProperties instanceof OA\Schema) {
            $this->walkSchemaTree($schema->unevaluatedProperties, $schemaVisitor, $refVisitor);
        }
        foreach ($schema->patternProperties ?? [] as $child) {
            $this->walkSchemaTree($child, $schemaVisitor, $refVisitor);
        }
        foreach ($schema->dependentSchemas ?? [] as $child) {
            $this->walkSchemaTree($child, $schemaVisitor, $refVisitor);
        }
        if ($schema->propertyNames instanceof OA\Schema) {
            $this->walkSchemaTree($schema->propertyNames, $schemaVisitor, $refVisitor);
        }

        // Conditional
        if ($schema->if instanceof OA\Schema) {
            $this->walkSchemaTree($schema->if, $schemaVisitor, $refVisitor);
        }
        if ($schema->then instanceof OA\Schema) {
            $this->walkSchemaTree($schema->then, $schemaVisitor, $refVisitor);
        }
        if ($schema->else instanceof OA\Schema) {
            $this->walkSchemaTree($schema->else, $schemaVisitor, $refVisitor);
        }
    }

    /**
     * @param list<OA\Example>|null $examples
     */
    protected function walkExampleRefs(?array $examples, callable $refVisitor): void
    {
        if (!$examples) {
            return;
        }

        foreach ($examples as $example) {
            $this->visitRef($example, $refVisitor);
        }
    }

    /**
     * @param list<OA\Security\Requirement> $security
     */
    protected function walkSecurityRefs(array $security, callable $refVisitor): void
    {
        foreach ($security as $requirement) {
            foreach (array_keys($requirement->toArray()) as $schemeName) {
                $refVisitor(new OA\Security\Scheme(ref: '#/components/securitySchemes/' . $schemeName));
            }
        }
    }

    protected function visitRef(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme $attribute, callable $refVisitor): void
    {
        if ($attribute->ref !== null) {
            $refVisitor($attribute);
        }
    }
}
