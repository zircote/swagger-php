<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\PipeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * Resolves FQCN-based $ref values to JSON Reference paths.
 *
 * Builds a map of class names to their component paths and rewrites
 * any $ref that looks like a FQCN into the proper #/components/... path.
 *
 * @implements PipeInterface<Specification>
 */
class Ref implements PipeInterface
{
    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function __invoke(mixed $payload): mixed
    {
        $refMap = $this->buildRefMap($payload);

        if ($refMap === []) {
            return null;
        }

        $this->resolveOperationRefs($payload, $refMap);
        $this->resolveSchemaRefs($payload, $refMap);
        $this->resolveParameterRefs($payload, $refMap);
        $this->resolveResponseRefs($payload, $refMap);
        $this->resolveHeaderRefs($payload, $refMap);
        $this->resolveRequestBodyRefs($payload, $refMap);

        return null;
    }

    /**
     * Build a map of FQCN → #/components/{type}/{name}.
     *
     * @return array<string, string>
     */
    protected function buildRefMap(Specification $specification): array
    {
        $map = [];

        foreach ($specification->schemas as $schema) {
            $name = $schema->schema ?? $schema->title;
            $fqcn = $this->getClassName($schema);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/schemas/' . $name;
            }
        }

        foreach ($specification->responses as $response) {
            $name = $response->response;
            $fqcn = $this->getClassName($response);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/responses/' . $name;
            }
        }

        foreach ($specification->requestBodies as $body) {
            $name = $body->request;
            $fqcn = $this->getClassName($body);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/requestBodies/' . $name;
            }
        }

        foreach ($specification->headers as $header) {
            $name = $header->header;
            $fqcn = $this->getClassName($header);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/headers/' . $name;
            }
        }

        foreach ($specification->parameters as $parameter) {
            $name = $parameter->parameter ?? $parameter->name;
            $fqcn = $this->getClassName($parameter);
            if ($name !== null && $fqcn !== null) {
                $map[$fqcn] = '#/components/parameters/' . $name;
            }
        }

        return $map;
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveOperationRefs(Specification $specification, array $refMap): void
    {
        foreach ($specification->operations as $operation) {
            if ($operation->parameters) {
                foreach ($operation->parameters as $parameter) {
                    $this->resolveRef($parameter, $refMap);
                    if ($parameter->schema instanceof OA\Schema) {
                        $this->resolveSchemaTree($parameter->schema, $refMap);
                    }
                }
            }

            if ($operation->requestBody instanceof OA\RequestBody) {
                $this->resolveRef($operation->requestBody, $refMap);
                $this->resolveMediaTypeRefs($operation->requestBody->content, $refMap);
            }

            if ($operation->responses) {
                foreach ($operation->responses as $response) {
                    $this->resolveRef($response, $refMap);
                    $this->resolveMediaTypeRefs($response->content, $refMap);
                    if ($response->headers) {
                        foreach ($response->headers as $header) {
                            $this->resolveRef($header, $refMap);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveSchemaRefs(Specification $specification, array $refMap): void
    {
        foreach ($specification->schemas as $schema) {
            $this->resolveSchemaTree($schema, $refMap);
        }
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveParameterRefs(Specification $specification, array $refMap): void
    {
        foreach ($specification->parameters as $parameter) {
            $this->resolveRef($parameter, $refMap);
            if ($parameter->schema instanceof OA\Schema) {
                $this->resolveSchemaTree($parameter->schema, $refMap);
            }
        }
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveResponseRefs(Specification $specification, array $refMap): void
    {
        foreach ($specification->responses as $response) {
            $this->resolveRef($response, $refMap);
            $this->resolveMediaTypeRefs($response->content, $refMap);
        }
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveHeaderRefs(Specification $specification, array $refMap): void
    {
        foreach ($specification->headers as $header) {
            $this->resolveRef($header, $refMap);
        }
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveRequestBodyRefs(Specification $specification, array $refMap): void
    {
        foreach ($specification->requestBodies as $body) {
            $this->resolveRef($body, $refMap);
            $this->resolveMediaTypeRefs($body->content, $refMap);
        }
    }

    /**
     * @param list<OA\MediaType>|null $mediaTypes
     * @param array<string, string>   $refMap
     */
    protected function resolveMediaTypeRefs(?array $mediaTypes, array $refMap): void
    {
        if ($mediaTypes === null) {
            return;
        }

        foreach ($mediaTypes as $mediaType) {
            if ($mediaType->schema instanceof OA\Schema) {
                $this->resolveSchemaTree($mediaType->schema, $refMap);
            }
        }
    }

    /**
     * Recursively resolve refs in a schema and its subschemas.
     *
     * @param array<string, string> $refMap
     */
    protected function resolveSchemaTree(OA\Schema $schema, array $refMap): void
    {
        $this->resolveRef($schema, $refMap);

        if ($schema->items instanceof OA\Schema) {
            $this->resolveSchemaTree($schema->items, $refMap);
        }

        if ($schema->properties) {
            foreach ($schema->properties as $property) {
                if ($property instanceof OA\Property && $property->schema instanceof OA\Schema) {
                    $this->resolveSchemaTree($property->schema, $refMap);
                }
            }
        }

        if ($schema->allOf) {
            foreach ($schema->allOf as $sub) {
                $this->resolveSchemaTree($sub, $refMap);
            }
        }
        if ($schema->anyOf) {
            foreach ($schema->anyOf as $sub) {
                $this->resolveSchemaTree($sub, $refMap);
            }
        }
        if ($schema->oneOf) {
            foreach ($schema->oneOf as $sub) {
                $this->resolveSchemaTree($sub, $refMap);
            }
        }
        if ($schema->not instanceof OA\Schema) {
            $this->resolveSchemaTree($schema->not, $refMap);
        }

        if ($schema->additionalProperties instanceof OA\Schema) {
            $this->resolveSchemaTree($schema->additionalProperties, $refMap);
        }
    }

    /**
     * @param array<string, string> $refMap
     */
    protected function resolveRef(OA\Schema|OA\Parameter|OA\Response|OA\Header|OA\RequestBody|OA\Link|OA\Example|OA\Security\Scheme $attribute, array $refMap): void
    {
        if ($attribute->ref === null) {
            return;
        }

        if (str_starts_with($attribute->ref, '#/')) {
            return;
        }

        if (isset($refMap[$attribute->ref])) {
            $attribute->ref = $refMap[$attribute->ref];
        }
    }

    protected function getClassName(OA\AbstractAttribute $attribute): ?string
    {
        $reflector = $attribute->getReflector();
        if ($reflector instanceof \ReflectionClass) {
            return $reflector->getName();
        }

        return null;
    }
}
