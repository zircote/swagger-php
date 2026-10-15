<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
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
 * A class carrying `#[OA\Components]` is the exception: it is a holder, so each of its members
 * declares a component of its own, named after the member.
 *
 * A parameter is keyed differently: its `name` is its identity in OpenAPI, so a parameter
 * component is keyed by it wherever it was declared, holder or not, and falls back to the class name only
 * when it has no name either.
 *
 * @implements PipeInterface<Specification>
 */
class Names implements PipeInterface
{
    /**
     * A security scheme is only ever declared inside `Components`, where the key is the whole
     * point, so nothing is inferred for it. A path item on a class is path-bound unless keyed
     * by hand, and a media type is only ever a component by hand, so neither is named after
     * the class either.
     */
    protected const SKIP_BUCKETS = ['securitySchemes', 'pathItems', 'mediaTypes'];

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
            $parameter->component ??= $parameter->name;
        }
    }

    /**
     * @param list<AttributeInterface> $components
     */
    protected function inferClassNames(array $components): void
    {
        foreach ($components as $component) {
            $name = $this->inferredName($component);
            if ($name === null) {
                continue;
            }

            $field = ComponentName::keyField($component);
            if ($field !== null && $component->{$field} === null) {
                $component->{$field} = $name;
            }
        }
    }

    /**
     * The name a component without a key is filed under, or null to leave it unnamed.
     *
     * - declared on a class: the class's short name, so a `$ref` given as the class resolves to it
     * - declared on a member of a class carrying `#[OA\Components]`: the member's own name,
     *   whatever kind of member it is
     * - anywhere else: null. The declaring class's name belongs to that class's own component,
     *   so the component stays inline
     *
     * Only a missing key is filled, and a parameter has been keyed by its `name` before this runs.
     */
    protected function inferredName(AttributeInterface $component): ?string
    {
        $reflector = $component->getReflector();

        if ($reflector instanceof \ReflectionClass) {
            return $component->getShortClassName();
        }

        $holder = $component->getClassReflector()?->getAttributes(OA\Components::class) ?? [];
        if ($holder !== [] && ($reflector instanceof \ReflectionMethod || $reflector instanceof \ReflectionProperty || $reflector instanceof \ReflectionClassConstant || $reflector instanceof \ReflectionParameter)) {
            return $reflector->getName();
        }

        return null;
    }
}
