<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Marks every path parameter that does not say otherwise as required.
 *
 * OpenAPI requires `required: true` on a parameter `in: path`. `OA\Parameter\Path` defaults to
 * it, and this gives a plain `OA\Parameter(in: 'path')` the same, whatever its PHP type or
 * default. An explicit `required` is left alone, and the compiler warns about one that is not `true`.
 *
 * @implements PipeInterface<Specification>
 */
class PathParameters implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        $payload->getWalker()->visit(OA\Parameter::class, function (OA\Parameter $parameter): void {
            if ($parameter->in === OA\ParameterIn::Path->value && $parameter->ref === null) {
                $parameter->required ??= true;
            }
        });

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }
}
