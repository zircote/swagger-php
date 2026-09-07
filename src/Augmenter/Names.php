<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Infers component names from PHP reflectors when not explicitly set.
 *
 * Sets schema and request body names from the class/interface/trait/enum short name,
 * and parameter component key from its name property.
 *
 * @implements PipeInterface<Specification>
 */
class Names implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        $this->inferSchemaNames($payload);
        $this->inferParameterNames($payload);
        $this->inferRequestBodyNames($payload);

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    /**
     * A schema takes its name from the class it is declared on. Declared anywhere else —
     * a method, a parameter — the class reflector belongs to the *declaring* class, whose
     * name is already taken by that class's own schema, so nothing is inferred.
     */
    protected function inferSchemaNames(Specification $specification): void
    {
        foreach ($specification->schemas as $schema) {
            if ($schema->getReflector() instanceof \ReflectionClass) {
                $schema->schema ??= $schema->getShortClassName();
            }
        }
    }

    protected function inferParameterNames(Specification $specification): void
    {
        foreach ($specification->parameters as $parameter) {
            $parameter->parameter ??= $parameter->name;
        }
    }

    /**
     * A request body declared on a class is named after it, so it can be referenced by class
     * name the way a schema can. Declared on a method it is inline, and needs no name.
     */
    protected function inferRequestBodyNames(Specification $specification): void
    {
        foreach ($specification->requestBodies as $requestBody) {
            if ($requestBody->getReflector() instanceof \ReflectionClass) {
                $requestBody->request ??= $requestBody->getShortClassName();
            }
        }
    }
}
