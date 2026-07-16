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
 * Sets schema name from the class/interface/trait/enum short name,
 * and parameter component key from its name property.
 *
 * @implements PipeInterface<Specification>
 */
class InferNames implements PipeInterface
{
    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function __invoke(mixed $payload): null
    {
        $this->inferSchemaNames($payload);
        $this->inferParameterNames($payload);

        return null;
    }

    protected function inferSchemaNames(Specification $specification): void
    {
        foreach ($specification->schemas as $schema) {
            $schema->schema ??= $schema->getShortClassName();
        }
    }

    protected function inferParameterNames(Specification $specification): void
    {
        foreach ($specification->parameters as $parameter) {
            $parameter->parameter ??= $parameter->name;
        }
    }
}
