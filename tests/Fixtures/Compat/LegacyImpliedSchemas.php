<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Compat;

use OpenApi\Augmenter\ImpliedSchemas;
use OpenApi\Spec as OA;

/**
 * An override with the signatures its methods were released with in 7.5.0, which a subclass
 * written against 7.5.0 still has. Loading it fails if one of them changed.
 */
class LegacyImpliedSchemas extends ImpliedSchemas
{
    protected function collect(?\Reflector $reflector, ?OA\Schema $schema = null): void
    {
        parent::collect($reflector, $schema);
    }
}
