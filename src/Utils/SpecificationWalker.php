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
    /**
     * Walk every Schema in the specification, recursively into nested schemas.
     *
     * @param callable(OA\Schema): void $visitor
     */
    public static function eachSchema(Specification $specification, callable $visitor): void
    {
        foreach ($specification->schemas as $schema) {
            self::walkSchemaTree($schema, $visitor);
        }

        foreach ($specification->operations as $operation) {
            if ($operation->parameters) {
                foreach ($operation->parameters as $parameter) {
                    if ($parameter->schema instanceof OA\Schema) {
                        self::walkSchemaTree($parameter->schema, $visitor);
                    }
                }
            }

            if ($operation->requestBody instanceof OA\RequestBody) {
                self::walkMediaTypeSchemas($operation->requestBody->content, $visitor);
            }

            if ($operation->responses) {
                foreach ($operation->responses as $response) {
                    self::walkMediaTypeSchemas($response->content, $visitor);
                }
            }
        }

        foreach ($specification->parameters as $parameter) {
            if ($parameter->schema instanceof OA\Schema) {
                self::walkSchemaTree($parameter->schema, $visitor);
            }
        }

        foreach ($specification->requestBodies as $body) {
            self::walkMediaTypeSchemas($body->content, $visitor);
        }

        foreach ($specification->responses as $response) {
            self::walkMediaTypeSchemas($response->content, $visitor);
        }

        foreach ($specification->headers as $header) {
            if ($header->schema instanceof OA\Schema) {
                self::walkSchemaTree($header->schema, $visitor);
            }
        }
    }

    /**
     * Walk every ref-bearing attribute in the specification.
     *
     * The visitor receives an attribute that has a non-null `$ref` property.
     * It may modify the ref in place.
     *
     * @param callable(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme): void $visitor
     */
    public static function eachRef(Specification $specification, callable $visitor): void
    {
        foreach ($specification->schemas as $schema) {
            self::walkSchemaTreeRefs($schema, $visitor);
        }

        foreach ($specification->operations as $operation) {
            if ($operation->parameters) {
                foreach ($operation->parameters as $parameter) {
                    self::visitRef($parameter, $visitor);
                    if ($parameter->schema instanceof OA\Schema) {
                        self::walkSchemaTreeRefs($parameter->schema, $visitor);
                    }
                    self::walkExampleRefs($parameter->examples, $visitor);
                }
            }

            if ($operation->requestBody instanceof OA\RequestBody) {
                self::visitRef($operation->requestBody, $visitor);
                self::walkMediaTypeRefs($operation->requestBody->content, $visitor);
            }

            if ($operation->responses) {
                foreach ($operation->responses as $response) {
                    self::visitRef($response, $visitor);
                    self::walkMediaTypeRefs($response->content, $visitor);
                    if ($response->headers) {
                        foreach ($response->headers as $header) {
                            self::visitRef($header, $visitor);
                            if ($header->schema instanceof OA\Schema) {
                                self::walkSchemaTreeRefs($header->schema, $visitor);
                            }
                            self::walkExampleRefs($header->examples, $visitor);
                        }
                    }
                    if ($response->links) {
                        foreach ($response->links as $link) {
                            self::visitRef($link, $visitor);
                        }
                    }
                }
            }

            if ($operation->security) {
                self::walkSecurityRefs($operation->security, $visitor);
            }
        }

        foreach ($specification->parameters as $parameter) {
            self::visitRef($parameter, $visitor);
            if ($parameter->schema instanceof OA\Schema) {
                self::walkSchemaTreeRefs($parameter->schema, $visitor);
            }
            self::walkExampleRefs($parameter->examples, $visitor);
        }

        foreach ($specification->requestBodies as $body) {
            self::visitRef($body, $visitor);
            self::walkMediaTypeRefs($body->content, $visitor);
        }

        foreach ($specification->responses as $response) {
            self::visitRef($response, $visitor);
            self::walkMediaTypeRefs($response->content, $visitor);
            if ($response->headers) {
                foreach ($response->headers as $header) {
                    self::visitRef($header, $visitor);
                    if ($header->schema instanceof OA\Schema) {
                        self::walkSchemaTreeRefs($header->schema, $visitor);
                    }
                    self::walkExampleRefs($header->examples, $visitor);
                }
            }
            if ($response->links) {
                foreach ($response->links as $link) {
                    self::visitRef($link, $visitor);
                }
            }
        }

        foreach ($specification->headers as $header) {
            self::visitRef($header, $visitor);
            if ($header->schema instanceof OA\Schema) {
                self::walkSchemaTreeRefs($header->schema, $visitor);
            }
            self::walkExampleRefs($header->examples, $visitor);
        }

        foreach ($specification->links as $link) {
            self::visitRef($link, $visitor);
        }

        foreach ($specification->examples as $example) {
            self::visitRef($example, $visitor);
        }

        if ($specification->openapi->security) {
            self::walkSecurityRefs($specification->openapi->security, $visitor);
        }
    }

    /**
     * @param callable(OA\Schema): void $visitor
     */
    protected static function walkSchemaTree(OA\Schema $schema, callable $visitor): void
    {
        $visitor($schema);

        if ($schema->properties) {
            foreach ($schema->properties as $property) {
                if ($property instanceof OA\Property && $property->schema instanceof OA\Schema) {
                    self::walkSchemaTree($property->schema, $visitor);
                }
            }
        }

        if ($schema->items instanceof OA\Schema) {
            self::walkSchemaTree($schema->items, $visitor);
        }
        if ($schema->additionalProperties instanceof OA\Schema) {
            self::walkSchemaTree($schema->additionalProperties, $visitor);
        }
        foreach ($schema->allOf ?? [] as $child) {
            self::walkSchemaTree($child, $visitor);
        }
        foreach ($schema->anyOf ?? [] as $child) {
            self::walkSchemaTree($child, $visitor);
        }
        foreach ($schema->oneOf ?? [] as $child) {
            self::walkSchemaTree($child, $visitor);
        }
        if ($schema->not instanceof OA\Schema) {
            self::walkSchemaTree($schema->not, $visitor);
        }
    }

    protected static function walkSchemaTreeRefs(OA\Schema $schema, callable $visitor): void
    {
        self::visitRef($schema, $visitor);

        if ($schema->discriminator instanceof OA\Discriminator && $schema->discriminator->mapping !== null) {
            foreach ($schema->discriminator->mapping as $ref) {
                $visitor(new OA\Schema(ref: $ref));
            }
        }

        if ($schema->properties) {
            foreach ($schema->properties as $property) {
                if ($property instanceof OA\Property && $property->schema instanceof OA\Schema) {
                    self::walkSchemaTreeRefs($property->schema, $visitor);
                }
            }
        }

        if ($schema->items instanceof OA\Schema) {
            self::walkSchemaTreeRefs($schema->items, $visitor);
        }
        if ($schema->additionalProperties instanceof OA\Schema) {
            self::walkSchemaTreeRefs($schema->additionalProperties, $visitor);
        }
        foreach ($schema->allOf ?? [] as $child) {
            self::walkSchemaTreeRefs($child, $visitor);
        }
        foreach ($schema->anyOf ?? [] as $child) {
            self::walkSchemaTreeRefs($child, $visitor);
        }
        foreach ($schema->oneOf ?? [] as $child) {
            self::walkSchemaTreeRefs($child, $visitor);
        }
        if ($schema->not instanceof OA\Schema) {
            self::walkSchemaTreeRefs($schema->not, $visitor);
        }
    }

    /**
     * @param list<OA\MediaType>|null   $mediaTypes
     * @param callable(OA\Schema): void $visitor
     */
    protected static function walkMediaTypeSchemas(?array $mediaTypes, callable $visitor): void
    {
        if (!$mediaTypes) {
            return;
        }

        foreach ($mediaTypes as $mediaType) {
            if ($mediaType->schema instanceof OA\Schema) {
                self::walkSchemaTree($mediaType->schema, $visitor);
            }
        }
    }

    /**
     * @param list<OA\MediaType>|null $mediaTypes
     */
    protected static function walkMediaTypeRefs(?array $mediaTypes, callable $visitor): void
    {
        if (!$mediaTypes) {
            return;
        }

        foreach ($mediaTypes as $mediaType) {
            if ($mediaType->schema instanceof OA\Schema) {
                self::walkSchemaTreeRefs($mediaType->schema, $visitor);
            }
            self::walkExampleRefs($mediaType->examples, $visitor);
        }
    }

    /**
     * @param list<OA\Example>|null $examples
     */
    protected static function walkExampleRefs(?array $examples, callable $visitor): void
    {
        if (!$examples) {
            return;
        }

        foreach ($examples as $example) {
            self::visitRef($example, $visitor);
        }
    }

    /**
     * @param list<OA\Security\Requirement> $security
     */
    protected static function walkSecurityRefs(array $security, callable $visitor): void
    {
        foreach ($security as $requirement) {
            foreach (array_keys($requirement->toArray()) as $schemeName) {
                $visitor(new OA\Security\Scheme(ref: '#/components/securitySchemes/' . $schemeName));
            }
        }
    }

    protected static function visitRef(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme $attribute, callable $visitor): void
    {
        if ($attribute->ref !== null) {
            $visitor($attribute);
        }
    }
}
