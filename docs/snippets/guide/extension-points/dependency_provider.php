<?php declare(strict_types=1);

namespace OpenApi\Snippets\Guide\ExtensionPoints;

use OpenApi\Contracts\DependencyProviderInterface;
use OpenApi\Specification;

final class MetadataDependencies implements DependencyProviderInterface
{
    public function dependencies(Specification $specification): iterable
    {
        foreach ($specification->operations as $operation) {
            yield from $operation->getMeta('my-integration.dependencies', []);
        }
    }
}
