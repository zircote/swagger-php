<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\ComponentNamer;
use OpenApi\Contracts\AttributeFactoryAwareInterface;
use OpenApi\Contracts\ComponentNamerAwareInterface;
use OpenApi\Contracts\ComponentNamerInterface;
use OpenApi\Specification;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\PipeInterface;

/**
 * Handle all scenarios related to PHP inheritance.
 *
 * Delegates to:
 * - Inheritance\Schemas
 * - Inheritance\Operations
 *
 * @implements PipeInterface<Specification>
 */
class Inheritance implements PipeInterface, AttributeFactoryAwareInterface, ComponentNamerAwareInterface
{
    protected AttributeFactory $attributeFactory;

    public function __construct(
        AttributeFactory $attributeFactory = new AttributeFactory(),
        protected ComponentNamerInterface $namer = new ComponentNamer\ShortName(),
    ) {
        $this->setAttributeFactory($attributeFactory);
    }

    public function __invoke(mixed $payload): mixed
    {
        (new Inheritance\Schemas($this->attributeFactory, $this->namer))->resolve($payload);
        (new Inheritance\Operations($this->attributeFactory))->resolve($payload);

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function setAttributeFactory(AttributeFactory $attributeFactory): static
    {
        $this->attributeFactory = $attributeFactory;

        return $this;
    }

    public function setNamer(ComponentNamerInterface $namer): static
    {
        $this->namer = $namer;

        return $this;
    }
}
