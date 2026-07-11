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
        foreach ($this->specification->schemas as $schema) {
            $this->walkSchemaTree($schema, $visitor);
        }

        foreach ($this->specification->operations as $operation) {
            $this->walkOperationSchemas($operation, $visitor);
        }

        foreach ($this->specification->pathItems as $pathItem) {
            if ($pathItem->parameters) {
                foreach ($pathItem->parameters as $parameter) {
                    if ($parameter->schema instanceof OA\Schema) {
                        $this->walkSchemaTree($parameter->schema, $visitor);
                    }
                }
            }
            if ($pathItem->responses) {
                foreach ($pathItem->responses as $response) {
                    $this->walkResponseSchemas($response, $visitor);
                }
            }
        }

        foreach ($this->specification->parameters as $parameter) {
            if ($parameter->schema instanceof OA\Schema) {
                $this->walkSchemaTree($parameter->schema, $visitor);
            }
        }

        foreach ($this->specification->requestBodies as $body) {
            $this->walkMediaTypeSchemas($body->content, $visitor);
        }

        foreach ($this->specification->responses as $response) {
            $this->walkResponseSchemas($response, $visitor);
        }

        foreach ($this->specification->headers as $header) {
            if ($header->schema instanceof OA\Schema) {
                $this->walkSchemaTree($header->schema, $visitor);
            }
        }
    }

    /**
     * Walk every ref-bearing attribute in the specification.
     *
     * @param callable(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme): void $visitor
     */
    public function eachRef(callable $visitor): void
    {
        foreach ($this->specification->schemas as $schema) {
            $this->walkSchemaTreeRefs($schema, $visitor);
        }

        foreach ($this->specification->operations as $operation) {
            $this->walkOperationRefs($operation, $visitor);
        }

        foreach ($this->specification->pathItems as $pathItem) {
            if ($pathItem->parameters) {
                foreach ($pathItem->parameters as $parameter) {
                    $this->visitRef($parameter, $visitor);
                    if ($parameter->schema instanceof OA\Schema) {
                        $this->walkSchemaTreeRefs($parameter->schema, $visitor);
                    }
                    $this->walkExampleRefs($parameter->examples, $visitor);
                }
            }
            if ($pathItem->responses) {
                foreach ($pathItem->responses as $response) {
                    $this->visitRef($response, $visitor);
                    $this->walkMediaTypeRefs($response->content, $visitor);
                    $this->walkResponseHeaderRefs($response, $visitor);
                    if ($response->links) {
                        foreach ($response->links as $link) {
                            $this->visitRef($link, $visitor);
                        }
                    }
                }
            }
        }

        foreach ($this->specification->parameters as $parameter) {
            $this->visitRef($parameter, $visitor);
            if ($parameter->schema instanceof OA\Schema) {
                $this->walkSchemaTreeRefs($parameter->schema, $visitor);
            }
            $this->walkExampleRefs($parameter->examples, $visitor);
        }

        foreach ($this->specification->requestBodies as $body) {
            $this->visitRef($body, $visitor);
            $this->walkMediaTypeRefs($body->content, $visitor);
        }

        foreach ($this->specification->responses as $response) {
            $this->visitRef($response, $visitor);
            $this->walkMediaTypeRefs($response->content, $visitor);
            $this->walkResponseHeaderRefs($response, $visitor);
            if ($response->links) {
                foreach ($response->links as $link) {
                    $this->visitRef($link, $visitor);
                }
            }
        }

        foreach ($this->specification->headers as $header) {
            $this->visitRef($header, $visitor);
            if ($header->schema instanceof OA\Schema) {
                $this->walkSchemaTreeRefs($header->schema, $visitor);
            }
            $this->walkExampleRefs($header->examples, $visitor);
        }

        foreach ($this->specification->links as $link) {
            $this->visitRef($link, $visitor);
        }

        foreach ($this->specification->examples as $example) {
            $this->visitRef($example, $visitor);
        }

        if ($this->specification->openapi->security) {
            $this->walkSecurityRefs($this->specification->openapi->security, $visitor);
        }
    }

    /**
     * @param callable(OA\Schema): void $visitor
     */
    protected function walkSchemaTree(OA\Schema $schema, callable $visitor): void
    {
        $visitor($schema);

        if ($schema->properties) {
            foreach ($schema->properties as $property) {
                if ($property instanceof OA\Property && $property->schema instanceof OA\Schema) {
                    $this->walkSchemaTree($property->schema, $visitor);
                }
            }
        }

        if ($schema->items instanceof OA\Schema) {
            $this->walkSchemaTree($schema->items, $visitor);
        }
        if ($schema->additionalProperties instanceof OA\Schema) {
            $this->walkSchemaTree($schema->additionalProperties, $visitor);
        }
        foreach ($schema->allOf ?? [] as $child) {
            $this->walkSchemaTree($child, $visitor);
        }
        foreach ($schema->anyOf ?? [] as $child) {
            $this->walkSchemaTree($child, $visitor);
        }
        foreach ($schema->oneOf ?? [] as $child) {
            $this->walkSchemaTree($child, $visitor);
        }
        if ($schema->not instanceof OA\Schema) {
            $this->walkSchemaTree($schema->not, $visitor);
        }
    }

    protected function walkSchemaTreeRefs(OA\Schema $schema, callable $visitor): void
    {
        $this->walkSchemaTree($schema, function (OA\Schema $schema) use ($visitor): void {
            $this->visitRef($schema, $visitor);

            if ($schema->discriminator instanceof OA\Discriminator && $schema->discriminator->mapping !== null) {
                foreach ($schema->discriminator->mapping as $ref) {
                    $visitor(new OA\Schema(ref: $ref));
                }
            }
        });
    }

    /**
     * @param callable(OA\Schema): void $visitor
     */
    protected function walkOperationSchemas(OA\Operation $operation, callable $visitor): void
    {
        if ($operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                if ($parameter->schema instanceof OA\Schema) {
                    $this->walkSchemaTree($parameter->schema, $visitor);
                }
            }
        }

        if ($operation->requestBody instanceof OA\RequestBody) {
            $this->walkMediaTypeSchemas($operation->requestBody->content, $visitor);
        }

        if ($operation->responses) {
            foreach ($operation->responses as $response) {
                $this->walkResponseSchemas($response, $visitor);
            }
        }

        if ($operation->callbacks) {
            foreach ($operation->callbacks as $callback) {
                if (is_array($callback)) {
                    array_walk_recursive($callback, function (mixed $value) use ($visitor): void {
                        if ($value instanceof OA\Operation) {
                            $this->walkOperationSchemas($value, $visitor);
                        }
                    });
                }
            }
        }
    }

    protected function walkOperationRefs(OA\Operation $operation, callable $visitor): void
    {
        if ($operation->parameters) {
            foreach ($operation->parameters as $parameter) {
                $this->visitRef($parameter, $visitor);
                if ($parameter->schema instanceof OA\Schema) {
                    $this->walkSchemaTreeRefs($parameter->schema, $visitor);
                }
                $this->walkExampleRefs($parameter->examples, $visitor);
            }
        }

        if ($operation->requestBody instanceof OA\RequestBody) {
            $this->visitRef($operation->requestBody, $visitor);
            $this->walkMediaTypeRefs($operation->requestBody->content, $visitor);
        }

        if ($operation->responses) {
            foreach ($operation->responses as $response) {
                $this->visitRef($response, $visitor);
                $this->walkMediaTypeRefs($response->content, $visitor);
                $this->walkResponseHeaderRefs($response, $visitor);
                if ($response->links) {
                    foreach ($response->links as $link) {
                        $this->visitRef($link, $visitor);
                    }
                }
            }
        }

        if ($operation->security) {
            $this->walkSecurityRefs($operation->security, $visitor);
        }

        if ($operation->callbacks) {
            foreach ($operation->callbacks as $callback) {
                if (is_array($callback)) {
                    array_walk_recursive($callback, function (mixed $value) use ($visitor): void {
                        if ($value instanceof OA\Operation) {
                            $this->walkOperationRefs($value, $visitor);
                        }
                    });
                }
            }
        }
    }

    /**
     * @param callable(OA\Schema): void $visitor
     */
    protected function walkResponseSchemas(OA\Response $response, callable $visitor): void
    {
        $this->walkMediaTypeSchemas($response->content, $visitor);

        if ($response->headers) {
            foreach ($response->headers as $header) {
                if ($header->schema instanceof OA\Schema) {
                    $this->walkSchemaTree($header->schema, $visitor);
                }
            }
        }
    }

    protected function walkResponseHeaderRefs(OA\Response $response, callable $visitor): void
    {
        if ($response->headers) {
            foreach ($response->headers as $header) {
                $this->visitRef($header, $visitor);
                if ($header->schema instanceof OA\Schema) {
                    $this->walkSchemaTreeRefs($header->schema, $visitor);
                }
                $this->walkExampleRefs($header->examples, $visitor);
            }
        }
    }

    /**
     * @param list<OA\MediaType>|null   $mediaTypes
     * @param callable(OA\Schema): void $visitor
     */
    protected function walkMediaTypeSchemas(?array $mediaTypes, callable $visitor): void
    {
        if (!$mediaTypes) {
            return;
        }

        foreach ($mediaTypes as $mediaType) {
            if ($mediaType->schema instanceof OA\Schema) {
                $this->walkSchemaTree($mediaType->schema, $visitor);
            }
        }
    }

    /**
     * @param list<OA\MediaType>|null $mediaTypes
     */
    protected function walkMediaTypeRefs(?array $mediaTypes, callable $visitor): void
    {
        if (!$mediaTypes) {
            return;
        }

        foreach ($mediaTypes as $mediaType) {
            if ($mediaType->schema instanceof OA\Schema) {
                $this->walkSchemaTreeRefs($mediaType->schema, $visitor);
            }
            $this->walkExampleRefs($mediaType->examples, $visitor);
        }
    }

    /**
     * @param list<OA\Example>|null $examples
     */
    protected function walkExampleRefs(?array $examples, callable $visitor): void
    {
        if (!$examples) {
            return;
        }

        foreach ($examples as $example) {
            $this->visitRef($example, $visitor);
        }
    }

    /**
     * @param list<OA\Security\Requirement> $security
     */
    protected function walkSecurityRefs(array $security, callable $visitor): void
    {
        foreach ($security as $requirement) {
            foreach (array_keys($requirement->toArray()) as $schemeName) {
                $visitor(new OA\Security\Scheme(ref: '#/components/securitySchemes/' . $schemeName));
            }
        }
    }

    protected function visitRef(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme $attribute, callable $visitor): void
    {
        if ($attribute->ref !== null) {
            $visitor($attribute);
        }
    }
}
