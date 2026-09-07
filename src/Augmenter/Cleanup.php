<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Specification;
use OpenApi\Specification\ComponentName;
use OpenApi\Utils\Config;
use OpenApi\Utils\JsonPointer;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Removes unreferenced components from the specification.
 *
 * Iterates multiple times to catch nested dependencies (a schema only
 * referenced by another unused schema should also be removed).
 *
 * @implements PipeInterface<Specification>
 */
class Cleanup implements PipeInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const MAX_ITERATIONS = 10;

    public function __construct(
        #[Config('Enables/disables removal of unreferenced components.')]
        protected bool $enabled = true,
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        if (!$this->enabled) {
            return null;
        }

        for ($i = 0; $i < self::MAX_ITERATIONS; ++$i) {
            if (!$this->cleanup($payload)) {
                return null;
            }
        }

        $this->logger?->warning('CleanUnused: maximum iterations ({max}) reached, some unused components may remain', [
            'max' => self::MAX_ITERATIONS,
        ]);

        return null;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Reduce;
    }

    protected function cleanup(Specification $specification): bool
    {
        $usedRefs = [];

        $specification->getWalker()->eachRef(static function (AttributeInterface $attribute) use (&$usedRefs): void {
            if (property_exists($attribute, 'ref') && $attribute->ref !== null) {
                $usedRefs[$attribute->ref] = true;
            }
        });

        $removed = false;
        foreach (ComponentName::BUCKETS as $bucket) {
            if ($this->removeUnused($specification, $bucket, $usedRefs)) {
                $removed = true;
            }
        }

        return $removed;
    }

    /**
     * A component with no key is left alone: nothing can reference it, so "unreferenced"
     * says nothing about whether it is wanted. The compiler reports it instead.
     *
     * @param array<string, true> $usedRefs
     */
    protected function removeUnused(Specification $specification, string $bucket, array $usedRefs): bool
    {
        $removed = false;
        foreach ($specification->{$bucket} as $index => $item) {
            $name = ComponentName::of($item);
            if ($name !== null && !isset($usedRefs[JsonPointer::ref('components', $bucket, $name)])) {
                unset($specification->{$bucket}[$index]);
                $removed = true;
            }
        }
        if ($removed) {
            $specification->{$bucket} = array_values($specification->{$bucket});
        }

        return $removed;
    }
}
