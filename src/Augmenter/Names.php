<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Specification;
use OpenApi\Specification\ComponentName;
use OpenApi\Utils\PipeInterface;

/**
 * Infers component keys from PHP reflectors when not explicitly set.
 *
 * A component declared on a class is named after that class, so it can be referenced by
 * class name. Declared anywhere else — a method, a parameter — the class reflector belongs
 * to the *declaring* class, whose name is already taken by that class's own component, so
 * nothing is inferred and the component stays inline.
 *
 * A parameter is the one exception: its `name` is its identity in OpenAPI, so a parameter
 * component is keyed by it wherever it was declared, and falls back to the class name only
 * when it has no name either.
 *
 * @implements PipeInterface<Specification>
 */
class Names implements PipeInterface
{
    /**
     * A security scheme is only ever declared inside `Components`, where the key is the whole
     * point, so nothing is inferred for it.
     */
    protected const SKIP_BUCKETS = ['securitySchemes'];

    public function __invoke(mixed $payload): mixed
    {
        $this->inferParameterNames($payload);

        foreach (array_diff(ComponentName::BUCKETS, static::SKIP_BUCKETS) as $bucket) {
            $this->inferClassNames($payload->{$bucket});
        }

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function inferParameterNames(Specification $specification): void
    {
        foreach ($specification->parameters as $parameter) {
            $parameter->parameter ??= $parameter->name;
        }
    }

    /**
     * @param list<AttributeInterface> $components
     */
    protected function inferClassNames(array $components): void
    {
        foreach ($components as $component) {
            if (!$component->getReflector() instanceof \ReflectionClass) {
                continue;
            }

            $field = ComponentName::keyField($component);
            if ($field !== null && $component->{$field} === null) {
                $component->{$field} = $component->getShortClassName();
            }
        }
    }
}
