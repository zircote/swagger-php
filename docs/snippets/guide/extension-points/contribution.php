<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Builder\Result;
use OpenApi\Spec as OA;
use OpenApi\Specification;

/**
 * Routes registered imperatively — no attribute anywhere, so nothing to scan.
 *
 * @param array<string, class-string> $routes path => the class documenting the response
 */
function buildFromRoutes(array $routes): Result
{
    return (new Builder())
        ->setMode(Mode::SPEC)
        ->addSource(new \ReflectionClass(Pet::class))
        ->withSpecification(function (Specification $specification) use ($routes): void {
            $specification->add(new OA\Info(title: 'Registered routes', version: '1.0.0'));

            foreach ($routes as $path => $model) {
                $specification->add(new OA\Operation\Get(path: $path, responses: [
                    new OA\Response(response: 200, description: 'OK', content: [
                        new OA\MediaType\Json(schema: new OA\Schema(ref: $model)),
                    ]),
                ]));
            }
        })
        ->build();
}
