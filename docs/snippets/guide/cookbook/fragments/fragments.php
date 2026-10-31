<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\Cookbook\Fragments;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\Pipeline;

/**
 * A new builder for each part; the host's own augmenters are registered here too.
 */
function partBuilder(): Builder
{
    return (new Builder())
        ->setMode(Mode::SPEC)
        ->setVersion('3.1.0')
        ->withAugmenters(fn (Pipeline $augmenters) => $augmenters->remove(Cleanup::class))
        ->withSpecification(fn (Specification $specification) => $specification->add(
            (new OA\Info(title: 'Part', version: '1.0.0'))->setMeta('host.filler', true)
        ));
}

/**
 * The schema of a class with `#[OA\Schema]`.
 *
 * @param class-string $class
 *
 * @return array<string, mixed>
 */
function buildModel(string $class): array
{
    $reflector = new \ReflectionClass($class);
    $document = partBuilder()->addSource($reflector)->build()->toArray();

    return $document['components']['schemas'][$reflector->getShortName()];
}

/**
 * A part keyed with `component:`, from the given `components` bucket.
 *
 * @return array<string, mixed>
 */
function buildPart(AttributeInterface $part, string $bucket): array
{
    $document = partBuilder()
        ->withSpecification(fn (Specification $specification) => $specification->add($part))
        ->build()
        ->toArray();

    return $document['components'][$bucket][$part->component];
}
