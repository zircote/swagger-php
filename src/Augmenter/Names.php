<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\ComponentNamer;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\ComponentNamerAwareInterface;
use OpenApi\Contracts\ComponentNamerInterface;
use OpenApi\Specification;
use OpenApi\Specification\ComponentName;
use OpenApi\Utils\PipeInterface;

/**
 * Infers component names from PHP reflectors when not explicitly set.
 *
 * The name is the one the component namer gives, or none, which leaves the component inline.
 * `ComponentNamer\ShortName`, the default, names a component declared on a class after the class,
 * and a member of an `#[OA\Components]` holder after the member.
 *
 * A parameter is named differently: its `name` is its identity in OpenAPI, so a parameter
 * component is named after it wherever it was declared, holder or not, and falls back to the class name only
 * when it has no name either.
 *
 * @implements PipeInterface<Specification>
 */
class Names implements PipeInterface, ComponentNamerAwareInterface
{
    /**
     * A security scheme is only ever declared inside `Components`, where the name is the whole
     * point, so nothing is inferred for it. A path item on a class is path-bound unless named
     * by hand, and a media type is only ever a component by hand, so neither is named after
     * the class either.
     */
    protected const SKIP_BUCKETS = ['securitySchemes', 'pathItems', 'mediaTypes'];

    public function __construct(
        protected ComponentNamerInterface $namer = new ComponentNamer\ShortName(),
    ) {
    }

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

    public function setNamer(ComponentNamerInterface $namer): static
    {
        $this->namer = $namer;

        return $this;
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
            $name = $this->namer->name($component);
            if ($name === null) {
                continue;
            }

            $field = ComponentName::keyField($component);
            if ($field !== null && $component->{$field} === null) {
                $component->{$field} = $name;
            }
        }
    }
}
