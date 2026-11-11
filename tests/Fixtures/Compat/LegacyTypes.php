<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Compat;

use OpenApi\Augmenter\Types;
use OpenApi\Spec as OA;

/**
 * An override with the signatures its methods were released with in 7.5.0, which a subclass
 * written against 7.5.0 still has. Loading it fails if one of them changed.
 */
class LegacyTypes extends Types
{
    protected function augmentProperty(OA\Property $property): void
    {
        parent::augmentProperty($property);
    }

    protected function augmentMethodProperty(OA\Property $property, \ReflectionMethod $method): void
    {
        parent::augmentMethodProperty($property, $method);
    }
}
