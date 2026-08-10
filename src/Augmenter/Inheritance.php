<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Specification;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\PipeInterface;

/**
 * Handle all scenarios related to PHP inheritance.
 *
 * @implements PipeInterface<Specification>
 */
class Inheritance implements PipeInterface
{
    protected AttributeFactory $attributeFactory;

    public function __construct(
        AttributeFactory $attributeFactory = new AttributeFactory(),
    ) {
        $this->setAttributeFactory($attributeFactory);
    }

    public function __invoke(mixed $payload): mixed
    {
        (new Inheritance\Schemas($this->attributeFactory))->resolve($payload);
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
}
