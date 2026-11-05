<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Builder;

use OpenApi\Builder;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Resolver;
use OpenApi\Specification;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\TypedList;

/**
 * The builder's hooks, as a plugin sees them.
 *
 * Each hook configures what the builder holds, the resolvers, augmenters, mergers, translators
 * and contributions. Plugins build on one another by adding to them, and their order decides
 * where each one's additions sit. Services such as the component namer, the type resolver, the
 * compiler and the version are not here: they belong to whoever runs the build.
 */
class Hooks
{
    public function __construct(protected Builder $builder)
    {
    }

    /**
     * @param callable(Resolver): (Resolver|void) $hook
     */
    public function withResolver(callable $hook): static
    {
        $this->builder->withResolver($hook);

        return $this;
    }

    /**
     * @param callable(Pipeline<Specification>): (Pipeline<Specification>|void) $hook
     */
    public function withAugmenters(callable $hook): static
    {
        $this->builder->withAugmenters($hook);

        return $this;
    }

    /**
     * @param callable(TypedList<MergerInterface>): (TypedList<MergerInterface>|void) $hook
     */
    public function withMergers(callable $hook): static
    {
        $this->builder->withMergers($hook);

        return $this;
    }

    /**
     * @param callable(AttributeFactory): (AttributeFactory|void) $hook
     */
    public function withAttributeFactory(callable $hook): static
    {
        $this->builder->withAttributeFactory($hook);

        return $this;
    }

    /**
     * @param callable(Specification): (Specification|void) $hook
     */
    public function withSpecification(callable $hook): static
    {
        $this->builder->withSpecification($hook);

        return $this;
    }
}
