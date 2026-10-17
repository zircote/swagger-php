<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Adds each tag declared on a method or a class to the operations declared there.
 *
 * A tag on a method goes to that method's operations, and a tag on a class to every operation
 * whose class it is, the ones on its methods included. An operation's own `tags` come first,
 * then its method's, then its class's, each name once. A tag with no operations to go to, such
 * as one on a class holding only `Info`, is left a declaration.
 *
 * An operation belongs to the class its reflector names. For one inherited into a child
 * controller that is the child, so it takes the child's class tags.
 *
 * @implements PipeInterface<Specification>
 */
class OperationTags implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        $byMethod = [];
        $byClass = [];

        foreach ($payload->tags as $tag) {
            if ($tag->name === null) {
                continue;
            }

            $reflector = $tag->getReflector();
            if ($reflector instanceof \ReflectionMethod) {
                $byMethod[$this->methodKey($reflector)][] = $tag->name;
            } elseif ($reflector instanceof \ReflectionClass) {
                $byClass[$reflector->getName()][] = $tag->name;
            }
        }

        if ($byMethod === [] && $byClass === []) {
            return null;
        }

        foreach ($payload->operations as $operation) {
            $reflector = $operation->getReflector();
            $added = [
                ...($reflector instanceof \ReflectionMethod ? $byMethod[$this->methodKey($reflector)] ?? [] : []),
                ...$byClass[$operation->getClassName() ?? ''] ?? [],
            ];

            if ($added !== []) {
                $operation->tags = array_values(array_unique([...$operation->tags ?? [], ...$added]));
            }
        }

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function methodKey(\ReflectionMethod $method): string
    {
        return $method->getDeclaringClass()->getName() . '::' . $method->getName();
    }
}
