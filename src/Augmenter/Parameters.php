<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Utils\PipeInterface;

/**
 * Gives a parameter that is only a `ref` the `name` and `in` of the component it points to.
 *
 * OpenAPI tells two parameters apart by name and location. A `ref` parameter has neither of its
 * own, so without them it cannot be matched with an inline parameter describing the same thing,
 * and `Merge\Operations` keeps both. Filled here, after `Augmenter\Types` has named a parameter
 * from its PHP parameter and `Augmenter\Refs` has rewritten class name refs, every parameter
 * reaching the merge carries its identity. It has to run before `Augmenter\Merge`, which the
 * phases see to: this is `Resolve`, and the merge is `Reduce` and later.
 *
 * Only unset fields are filled, and the output does not change: a parameter with a `ref` is
 * compiled to the reference alone.
 *
 * @implements PipeInterface<Specification>
 */
class Parameters implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        $index = null;

        foreach ([...$payload->operations, ...$payload->pathItems] as $holder) {
            foreach ($holder->parameters ?? [] as $parameter) {
                if ($parameter->ref === null || ($parameter->name !== null && $parameter->in !== null)) {
                    continue;
                }

                $index ??= $payload->buildComponentIndex();
                $this->fillFromComponent($parameter, $index);
            }
        }

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function fillFromComponent(OA\Parameter $parameter, ComponentIndex $index): void
    {
        $ref = $parameter->ref instanceof OA\Schema\Ref ? $parameter->ref->ref : $parameter->ref;
        $target = is_string($ref) ? $index->findParameter($ref) : null;
        if (!$target instanceof OA\Parameter) {
            return;
        }

        $parameter->name ??= $target->name;
        $parameter->in ??= $target->in;
    }
}
