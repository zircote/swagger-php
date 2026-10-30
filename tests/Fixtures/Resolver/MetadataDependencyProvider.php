<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Contracts\DependencyProviderInterface;
use OpenApi\Specification;

class MetadataDependencyProvider implements DependencyProviderInterface
{
    public function dependencies(Specification $specification): iterable
    {
        foreach ([...$specification->operations, ...$specification->schemas] as $attribute) {
            yield from $attribute->getMeta('dependencies', []);
        }
    }
}
