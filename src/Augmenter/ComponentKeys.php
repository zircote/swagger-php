<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;
use OpenApi\Specification\ComponentName;
use OpenApi\Utils\PipeInterface;

/**
 * Keys the named components beside a class's own, so the class name means its own one.
 *
 * A class declaring several components in one bucket, one without a name and the others named, is
 * described once as itself and once per name. The one without a name keeps the class as its key,
 * so a class-name `$ref` or a property typed with the class reaches it. Each named one is keyed by
 * the class and its name, and is reached by name. Where none or several are without a name, the
 * class still names several components.
 *
 * @implements PipeInterface<Specification>
 */
class ComponentKeys implements PipeInterface
{
    public function __invoke(mixed $payload): mixed
    {
        foreach (ComponentName::BUCKETS as $bucket) {
            foreach ($this->byClass($payload->{$bucket}) as $components) {
                $unnamed = array_filter($components, static fn (AttributeInterface $component): bool => ComponentName::of($component) === null);
                if (count($components) < 2 || count($unnamed) !== 1) {
                    continue;
                }

                foreach ($components as $component) {
                    $name = ComponentName::of($component);
                    if ($name !== null) {
                        $component->setComponentKey(new ComponentKey($component->getComponentKey()->class, ['component' => $name]));
                    }
                }
            }
        }

        return null;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    /**
     * The components with a plain key, grouped by their class.
     *
     * @param list<AttributeInterface> $components
     *
     * @return array<string, list<AttributeInterface>>
     */
    protected function byClass(array $components): array
    {
        $grouped = [];
        foreach ($components as $component) {
            $key = $component->getComponentKey();
            if ($key instanceof ComponentKey && $key->isPlain()) {
                $grouped[$key->class][] = $component;
            }
        }

        return $grouped;
    }
}
