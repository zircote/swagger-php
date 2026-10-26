<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SchemaQueryParameterAugmenter;

use OpenApi\Augmenter\Group;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Custom augmenter to translate the vendor extension `query-args-$ref` into query parameters.
 *
 * The extension names a class, and each property of that class's schema becomes a query parameter.
 */
class SchemaQueryParameter implements PipeInterface
{
    public const REF = 'query-args-$ref';

    /**
     * @param Specification $payload
     */
    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->operations as $operation) {
            if (!array_key_exists(self::REF, $operation->x ?? [])) {
                continue;
            }

            $class = $operation->x[self::REF];
            if (!is_string($class)) {
                throw new \InvalidArgumentException('Value of `x.' . self::REF . '` must be a string');
            }

            $schema = $this->schemaFor($payload, $class);
            if (!$schema instanceof OA\Schema) {
                throw new \InvalidArgumentException('Value of `x.' . self::REF . "` names a class without a schema: `{$class}`");
            }

            $this->expandQueryArgs($operation, $schema);
            $this->cleanUp($operation);
        }

        return $payload;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }

    private function schemaFor(Specification $specification, string $class): ?OA\Schema
    {
        foreach ($specification->schemas as $schema) {
            if ($schema->getClassReflector()?->getName() === ltrim($class, '\\')) {
                return $schema;
            }
        }

        return null;
    }

    /**
     * Expand the given operation by adding a query parameter for each property of the given schema.
     */
    private function expandQueryArgs(OA\Operation $operation, OA\Schema $schema): void
    {
        foreach ($schema->properties ?? [] as $property) {
            $operation->parameters[] = new OA\Parameter\Query(
                name: $property->property,
                required: in_array($property->property, $schema->required ?? [], true),
                schema: new OA\Schema(
                    type: $property->schema?->type,
                    format: $property->schema?->format,
                    nullable: $property->schema?->nullable,
                ),
                example: $property->schema?->example,
            );
        }
    }

    private function cleanUp(OA\Operation $operation): void
    {
        unset($operation->x[self::REF]);
        if (!$operation->x) {
            $operation->x = null;
        }
    }
}
