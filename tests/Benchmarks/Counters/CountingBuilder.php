<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Counters;

use OpenApi\Builder;
use OpenApi\Specification;
use OpenApi\Utils\AttributeFactory;

/**
 * A builder whose source inspector, resolver and specification count what they do.
 *
 * Classic mode uses none of them, so its counts stay at zero.
 */
class CountingBuilder extends Builder
{
    private ?CountingSpecification $specification = null;

    public function __construct()
    {
        parent::__construct();

        $this->attributeFactory = new AttributeFactory(new CountingSourceInspector());
        $this->resolver = new CountingResolver();
    }

    /**
     * @return array{read: int, passes: int, unresolved: int, walks: int}
     */
    public function counts(): array
    {
        $sourceInspector = $this->getAttributeFactory()->getSourceInspector();
        $resolver = $this->getResolver();
        assert($sourceInspector instanceof CountingSourceInspector && $resolver instanceof CountingResolver);

        return [
            'read' => $sourceInspector->read,
            'passes' => $resolver->passes,
            'unresolved' => $resolver->unresolved,
            'walks' => $this->specification?->walks() ?? 0,
        ];
    }

    protected function createSpecification(): Specification
    {
        return $this->specification = new CountingSpecification();
    }
}
