<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures;

use OpenApi\Assembler;
use OpenApi\Contracts\AttributeFactoryAwareInterface;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\ComponentNamerAwareInterface;
use OpenApi\Contracts\ComponentNamerInterface;
use OpenApi\Contracts\KeyResolverInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Contracts\TypeResolverAwareInterface;
use OpenApi\Resolver\Concerns\KeyResolverTrait;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;
use OpenApi\Type\TypeResolver;
use OpenApi\Utils\AttributeFactory;

/**
 * An augmenter, resolver and merger that does nothing but keep the services the builder hands it.
 */
class ServiceRecorder implements AttributeFactoryAwareInterface, ComponentNamerAwareInterface, KeyResolverInterface, MergerInterface, TypeResolverAwareInterface
{
    use KeyResolverTrait;

    public ?AttributeFactory $attributeFactory = null;

    public ?ComponentNamerInterface $namer = null;

    public ?TypeResolver $typeResolver = null;

    public function __invoke(Specification $specification): Specification
    {
        return $specification;
    }

    public function resolveKey(ComponentKey $key, Assembler $assembler): bool
    {
        return false;
    }

    public function supports(string $class): bool
    {
        return false;
    }

    public function identity(AttributeInterface $attribute): ?string
    {
        return null;
    }

    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface
    {
        return $later;
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

    public function setTypeResolver(TypeResolver $typeResolver): static
    {
        $this->typeResolver = $typeResolver;

        return $this;
    }
}
